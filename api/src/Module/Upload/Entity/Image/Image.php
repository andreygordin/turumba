<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Entity\Image;

use App\Module\Upload\Service\FileHandler\FileHandlerInterface;
use DomainException;

class Image
{
    use FormatTrait;

    private Id $id;
    private Format $originalFormat;
    private FileHandlerInterface $fileHandler;
    private VariationCollection $variations;

    public function __construct(Id $id, Format $originalFormat, FileHandlerInterface $fileHandler)
    {
        $this->id = $id;
        $this->originalFormat = $originalFormat;
        $this->fileHandler = $fileHandler;
        $this->formats = new FormatCollection();
        $this->variations = new VariationCollection();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getOriginalFormat(): Format
    {
        return $this->originalFormat;
    }

    public function getFileHandler(): FileHandlerInterface
    {
        return $this->fileHandler;
    }

    public function setFileHandler(FileHandlerInterface $fileHandler): void
    {
        $this->fileHandler = $fileHandler;
    }

    /**
     * @return Variation[]
     */
    public function getVariations(): array
    {
        /** @var Variation[] */
        return $this->variations->toArray();
    }

    public function getVariation(Preset $preset): ?Variation
    {
        return $this->variations->findByPreset($preset);
    }

    public function hasVariations(): bool
    {
        return !$this->variations->isEmpty();
    }

    public function hasVariation(Preset $preset): bool
    {
        return $this->getVariation($preset) !== null;
    }

    public function createVariation(Preset $preset): void
    {
        if ($this->hasVariation($preset)) {
            throw new DomainException('Image already has a variation for this preset');
        }

        $variation = new Variation($preset);
        $this->variations->add($variation);
    }

    public function removeVariation(Variation $variation): void
    {
        $this->variations->removeValue($variation);
    }
}
