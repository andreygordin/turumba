<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Entity\Image;

use App\Module\Upload\Service\FileHandler\LocalFileHandlerFactory;
use App\Module\Upload\Service\PathGenerator;
use App\Module\Upload\Service\PresetCollection;
use App\Module\Upload\Service\Saver\SaverFactory;
use App\Module\Upload\Service\StreamHandler\ImageStreamHandler;
use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\Assert\Assert;

class ImageUnitOfWork
{
    private const STATE_NEW = 1;
    private const STATE_MANAGED = 2;
    private const STATE_REMOVED = 3;

    /**
     * @var PresetCollection
     */
    private PresetCollection $presetCollection;

    private LocalFileHandlerFactory $localFileHandlerFactory;

    private SaverFactory $saverFactory;

    private PathGenerator $pathGenerator;

    private Filesystem $fileSystem;

    /**
     * @var ImageCollection
     */
    private ImageCollection $newImages;

    /**
     * @var ImageCollection
     */
    private ImageCollection $managedImages;

    /**
     * @var ImageCollection
     */
    private ImageCollection $removedImages;

    /**
     * @var array<string,int>
     */
    private array $identityMap = [];

    public function __construct(
        PresetCollection $presetCollection,
        LocalFileHandlerFactory $localFileHandlerFactory,
        SaverFactory $saverFactory,
        PathGenerator $pathGenerator,
        Filesystem $fileSystem
    ) {
        $this->presetCollection = $presetCollection;
        $this->localFileHandlerFactory = $localFileHandlerFactory;
        $this->saverFactory = $saverFactory;
        $this->pathGenerator = $pathGenerator;
        $this->fileSystem = $fileSystem;
        $this->newImages = new ImageCollection();
        $this->managedImages = new ImageCollection();
        $this->removedImages = new ImageCollection();
    }

    public function persist(Image $image): void
    {
        $currentState = $this->getState($image);

        if ($currentState === self::STATE_MANAGED) {
            return;
        }

        if ($currentState === self::STATE_REMOVED) {
            $this->setState($image, self::STATE_MANAGED);
            return;
        }

        $this->setState($image, self::STATE_NEW);
    }

    public function remove(Image $image): void
    {
        $currentState = $this->getState($image);

        if ($currentState === null) {
            return;
        }

        if ($currentState === self::STATE_NEW) {
            $this->unsetState($image);
            return;
        }

        $this->setState($image, self::STATE_REMOVED);
    }

    public function find(Id $id): ?Image
    {
        $state = $this->getStateById($id);
        if ($state !== null) {
            return $this->getCollectionForState($state)->findById($id);
        }

        $image = $this->findOriginal($id);
        if ($image === null) {
            return null;
        }

        $this->restoreFormats($image);
        $this->restoreVariations($image);

        $this->setState($image, self::STATE_MANAGED);

        return $image;
    }

    public function exists(Id $id): bool
    {
        $imageDir = $this->pathGenerator->getDirPath($id->getValue());
        return $this->fileSystem->exists($imageDir);
    }

    public function commit(): void
    {
        /** @var Image $image */
        foreach ($this->newImages as $image) {
            $this->saveOriginal($image);
            $this->saveFormats($image);
            $this->saveVariations($image);
        }

        /** @var Image $image */
        foreach ($this->managedImages as $image) {
            $this->saveFormats($image);
            $this->saveVariations($image);
            $this->removeMissingFormats($image);
            $this->removeMissingVariations($image);
        }

        /** @var Image $image */
        foreach ($this->removedImages as $image) {
            $this->removeAll($image);
            $this->unsetState($image);
        }

        /** @var Image $image */
        foreach ($this->newImages as $image) {
            $this->setState($image, self::STATE_MANAGED);
        }
    }

    private function getState(Image $image): ?int
    {
        return $this->getStateById($image->getId());
    }

    private function getStateById(Id $id): ?int
    {
        $idValue = $id->getValue();
        return $this->identityMap[$idValue] ?? null;
    }

    private function setState(Image $image, int $state): void
    {
        $this->assertState($state);

        if ($state === $this->getState($image)) {
            return;
        }

        $this->unsetState($image);
        $this->getCollectionForState($state)->add($image);
        $idValue = $image->getId()->getValue();
        $this->identityMap[$idValue] = $state;
    }

    private function unsetState(Image $image): void
    {
        $state = $this->getState($image);

        if ($state === null) {
            return;
        }

        $collection = $this->getCollectionForState($state);
        $collection->removeValue($image);
        $idValue = $image->getId()->getValue();
        unset($this->identityMap[$idValue]);
    }

    private function getCollectionForState(int $state): ImageCollection
    {
        $this->assertState($state);

        $map = [
            self::STATE_NEW => $this->newImages,
            self::STATE_MANAGED => $this->managedImages,
            self::STATE_REMOVED => $this->removedImages,
        ];

        return $map[$state];
    }

    private function assertState(int $state): void
    {
        Assert::inArray($state, [self::STATE_NEW, self::STATE_MANAGED, self::STATE_REMOVED]);
    }

    private function findOriginal(Id $id): ?Image
    {
        if (!$this->exists($id)) {
            return null;
        }
        foreach ($this->getExtensionsOrderedByPriority() as $ext) {
            $filePath = $this->pathGenerator->getFilePath($id->getValue(), $ext);
            if ($this->fileSystem->exists($filePath)) {
                $format = Format::createForExtension($ext);
                $fileHandler = $this->localFileHandlerFactory->create($filePath);
                return new Image($id, $format, $fileHandler);
            }
        }
        throw new RuntimeException('Image directory exists but the original file is not found');
    }

    private function restoreFormats(Image $image): void
    {
        $exts = array_diff(
            $this->getExtensionsOrderedByPriority(),
            [$image->getOriginalFormat()->getExtension()]
        );
        foreach ($exts as $ext) {
            $filePath = $this->pathGenerator->getFilePath($image->getId()->getValue(), $ext);
            if ($this->fileSystem->exists($filePath)) {
                $format = Format::createForExtension($ext);
                $image->addFormat($format);
            }
        }
    }

    private function restoreVariations(Image $image): void
    {
        /**
         * @var string $name
         * @var Preset $preset
         */
        foreach ($this->presetCollection as $name => $preset) {
            foreach ($this->getExtensionsOrderedByPriority() as $ext) {
                $filePath = $this->pathGenerator->getFilePath($image->getId()->getValue(), $ext, $name);
                if (!$this->fileSystem->exists($filePath)) {
                    continue;
                }
                if (!$image->hasVariation($preset)) {
                    $image->createVariation($preset);
                }
                /** @var Variation $variation */
                $variation = $image->getVariation($preset);
                $format = Format::createForExtension($ext);
                $variation->addFormat($format);
            }
        }
    }

    private function saveOriginal(Image $image): void
    {
        $idValue = $image->getId()->getValue();

        $dirPath = $this->pathGenerator->getDirPath($idValue);
        $this->fileSystem->mkdir($dirPath);
        $this->fileSystem->chmod($dirPath, 0777);

        $originalFormat = $image->getOriginalFormat();
        $filePath = $this->pathGenerator->getFilePath($idValue, $originalFormat->getExtension());
        $this->saverFactory
            ->create($image->getFileHandler()->getStream())
            ->save($filePath, $originalFormat->getMimeType());

        $localFileHandler = $this->localFileHandlerFactory->create($filePath);
        $image->setFileHandler($localFileHandler);
    }

    private function saveFormats(Image $image): void
    {
        $idValue = $image->getId()->getValue();
        foreach ($image->getFormats() as $format) {
            $filePath = $this->pathGenerator->getFilePath($idValue, $format->getExtension());
            if (!$this->fileSystem->exists($filePath)) {
                $this->saverFactory
                    ->create($image->getFileHandler()->getStream())
                    ->save($filePath, $format->getMimeType());
            }
        }
    }

    private function saveVariations(Image $image): void
    {
        $idValue = $image->getId()->getValue();
        /** @var Variation $variation */
        foreach ($image->getVariations() as $variation) {
            foreach ($variation->getFormats() as $format) {
                $preset = $variation->getPreset();
                /** @var string|false $presetName */
                $presetName = $this->presetCollection->indexOf($preset);
                if ($presetName === false) {
                    throw new RuntimeException('Unknown preset');
                }
                $filePath = $this->pathGenerator->getFilePath($idValue, $format->getExtension(), $presetName);
                if (!$this->fileSystem->exists($filePath)) {
                    $streamHandlerCallback = function (ImageStreamHandler $streamHandler) use ($preset): void {
                        $streamHandler->setPreset($preset);
                    };
                    $this->saverFactory
                        ->create($image->getFileHandler()->getStream())
                        ->save($filePath, $format->getMimeType(), $streamHandlerCallback);
                }
            }
        }
    }

    private function removeMissingFormats(Image $image): void
    {
        $exts = array_diff(
            $this->getExtensionsOrderedByPriority(),
            [$image->getOriginalFormat()->getExtension()]
        );
        foreach ($exts as $ext) {
            $format = Format::createForExtension($ext);
            $filePath = $this->pathGenerator->getFilePath($image->getId()->getValue(), $ext);
            if (!$image->hasFormat($format) && $this->fileSystem->exists($filePath)) {
                $this->fileSystem->remove($filePath);
            }
        }
    }

    private function removeMissingVariations(Image $image): void
    {
        /**
         * @var string $name
         * @var Preset $preset
         */
        foreach ($this->presetCollection as $name => $preset) {
            foreach ($this->getExtensionsOrderedByPriority() as $ext) {
                $format = Format::createForExtension($ext);
                $filePath = $this->pathGenerator->getFilePath($image->getId()->getValue(), $ext, $name);
                /** @psalm-suppress PossiblyNullReference */
                $imageHasFormat = $image->hasVariation($preset)
                    && $image->getVariation($preset)->hasFormat($format);
                if (!$imageHasFormat && $this->fileSystem->exists($filePath)) {
                    $this->fileSystem->remove($filePath);
                }
            }
        }
    }

    private function removeAll(Image $image): void
    {
        $idValue = $image->getId()->getValue();
        $dirPath = $this->pathGenerator->getDirPath($idValue);
        $this->fileSystem->remove($dirPath);
    }

    /**
     * @return string[]
     */
    private function getExtensionsOrderedByPriority(): array
    {
        return ['png', 'jpg', 'webp'];
    }
}
