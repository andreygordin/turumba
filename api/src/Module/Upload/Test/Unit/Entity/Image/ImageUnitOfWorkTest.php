<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Test\Unit\Entity\Image;

use App\Module\Upload\Entity\Image\Id;
use App\Module\Upload\Entity\Image\Image;
use App\Module\Upload\Entity\Image\ImageCollection;
use App\Module\Upload\Entity\Image\ImageUnitOfWork;
use App\Module\Upload\Service\FileHandler\LocalFileHandler;
use App\Module\Upload\Service\FileHandler\LocalFileHandlerFactory;
use App\Module\Upload\Service\PathGenerator;
use App\Module\Upload\Service\PresetCollection;
use App\Module\Upload\Service\Saver\Saver;
use App\Module\Upload\Service\Saver\SaverFactory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Filesystem\Filesystem;

class ImageUnitOfWorkTest extends TestCase
{
    public function testDefault(): void
    {
        $uow = $this->createUow();

        self::assertCount(0, $this->getNewImages($uow));
        self::assertCount(0, $this->getManagedImages($uow));
        self::assertCount(0, $this->getRemovedImages($uow));
        self::assertCount(0, $this->getIdentityMap($uow));
    }

    public function testPersistUnknown(): void
    {
        $uow = $this->createUow();

        $image = $this->createStub(Image::class);
        $image->method('getId')->willReturn($id = Id::generate());

        $uow->persist($image);

        self::assertCount(1, $this->getNewImages($uow));
        /** @psalm-suppress InvalidArgument */
        self::assertTrue($this->getNewImages($uow)->contains($image));
        self::assertCount(0, $this->getManagedImages($uow));
        self::assertCount(0, $this->getRemovedImages($uow));
        self::assertArrayHasKey($id->getValue(), $this->getIdentityMap($uow));
    }

    public function testFindUnknown(): void
    {
        $uow = $this->createUow();

        $id = Id::generate();

        $image = $uow->find($id);

        self::assertInstanceOf(Image::class, $image);
        self::assertCount(0, $this->getNewImages($uow));
        self::assertCount(1, $this->getManagedImages($uow));
        /** @psalm-suppress InvalidArgument */
        self::assertTrue($this->getManagedImages($uow)->contains($image));
        self::assertCount(0, $this->getRemovedImages($uow));
        self::assertArrayHasKey($id->getValue(), $this->getIdentityMap($uow));
    }

    public function testFindNotFound(): void
    {
        $uow = $this->createUow(false);

        $id = Id::generate();

        $image = $uow->find($id);

        self::assertNull($image);
        self::assertCount(0, $this->getNewImages($uow));
        self::assertCount(0, $this->getManagedImages($uow));
        self::assertCount(0, $this->getRemovedImages($uow));
        self::assertCount(0, $this->getIdentityMap($uow));
    }

    public function testRemoveUnknown(): void
    {
        $uow = $this->createUow();

        $image = $this->createStub(Image::class);
        $image->method('getId')->willReturn($id = Id::generate());

        $uow->remove($image);

        self::assertCount(0, $this->getNewImages($uow));
        self::assertCount(0, $this->getManagedImages($uow));
        self::assertCount(0, $this->getRemovedImages($uow));
        self::assertCount(0, $this->getIdentityMap($uow));
    }

    public function testPersistNew(): void
    {
        $uow = $this->createUow();

        $image = $this->createStub(Image::class);
        $image->method('getId')->willReturn($id = Id::generate());

        $uow->persist($image);
        $uow->persist($image);

        self::assertCount(1, $this->getNewImages($uow));
        /** @psalm-suppress InvalidArgument */
        self::assertTrue($this->getNewImages($uow)->contains($image));
        self::assertCount(0, $this->getManagedImages($uow));
        self::assertCount(0, $this->getRemovedImages($uow));
        self::assertArrayHasKey($id->getValue(), $this->getIdentityMap($uow));
    }

    public function testFindNew(): void
    {
        $uow = $this->createUow();

        $image = $this->createStub(Image::class);
        $image->method('getId')->willReturn($id = Id::generate());

        $uow->persist($image);
        $foundImage = $uow->find($id);

        self::assertSame($image, $foundImage);

        self::assertCount(1, $this->getNewImages($uow));
        /** @psalm-suppress InvalidArgument */
        self::assertTrue($this->getNewImages($uow)->contains($image));
        self::assertCount(0, $this->getManagedImages($uow));
        self::assertCount(0, $this->getRemovedImages($uow));
        self::assertArrayHasKey($id->getValue(), $this->getIdentityMap($uow));
    }

    public function testRemoveNew(): void
    {
        $uow = $this->createUow();

        $image = $this->createStub(Image::class);
        $image->method('getId')->willReturn($id = Id::generate());

        $uow->persist($image);
        $uow->remove($image);

        self::assertCount(0, $this->getNewImages($uow));
        self::assertCount(0, $this->getManagedImages($uow));
        self::assertCount(0, $this->getRemovedImages($uow));
        self::assertCount(0, $this->getIdentityMap($uow));
    }

    public function testPersistManaged(): void
    {
        $uow = $this->createUow();

        $id = Id::generate();

        /** @var Image $image */
        $image = $uow->find($id);
        $uow->persist($image);

        self::assertCount(0, $this->getNewImages($uow));
        self::assertCount(1, $this->getManagedImages($uow));
        /** @psalm-suppress InvalidArgument */
        self::assertTrue($this->getManagedImages($uow)->contains($image));
        self::assertCount(0, $this->getRemovedImages($uow));
        self::assertArrayHasKey($id->getValue(), $this->getIdentityMap($uow));
    }

    public function testFindManaged(): void
    {
        $uow = $this->createUow();

        $id = Id::generate();

        /** @var Image $image */
        $image = $uow->find($id);

        /** @var Image $sameImage */
        $sameImage = $uow->find($id);

        self::assertSame($image, $sameImage);
        self::assertCount(0, $this->getNewImages($uow));
        self::assertCount(1, $this->getManagedImages($uow));
        /** @psalm-suppress InvalidArgument */
        self::assertTrue($this->getManagedImages($uow)->contains($image));
        self::assertCount(0, $this->getRemovedImages($uow));
        self::assertArrayHasKey($id->getValue(), $this->getIdentityMap($uow));
    }

    public function testRemoveManaged(): void
    {
        $uow = $this->createUow();

        $id = Id::generate();

        /** @var Image $image */
        $image = $uow->find($id);
        $uow->remove($image);

        self::assertCount(0, $this->getNewImages($uow));
        self::assertCount(0, $this->getManagedImages($uow));
        self::assertCount(1, $this->getRemovedImages($uow));
        /** @psalm-suppress InvalidArgument */
        self::assertTrue($this->getRemovedImages($uow)->contains($image));
        self::assertArrayHasKey($id->getValue(), $this->getIdentityMap($uow));
    }

    public function testPersistManagedAndRemoved(): void
    {
        $uow = $this->createUow();

        $id = Id::generate();

        /** @var Image $image */
        $image = $uow->find($id);
        $uow->remove($image);
        $uow->persist($image);

        self::assertCount(0, $this->getNewImages($uow));
        self::assertCount(1, $this->getManagedImages($uow));
        /** @psalm-suppress InvalidArgument */
        self::assertTrue($this->getManagedImages($uow)->contains($image));
        self::assertCount(0, $this->getRemovedImages($uow));
        self::assertArrayHasKey($id->getValue(), $this->getIdentityMap($uow));
    }

    public function testFindManagedAndRemoved(): void
    {
        $uow = $this->createUow();

        $id = Id::generate();

        /** @var Image $image */
        $image = $uow->find($id);
        $uow->remove($image);

        /** @var Image $image */
        $sameImage = $uow->find($id);

        self::assertSame($image, $sameImage);
        self::assertCount(0, $this->getNewImages($uow));
        self::assertCount(0, $this->getManagedImages($uow));
        self::assertCount(1, $this->getRemovedImages($uow));
        /** @psalm-suppress InvalidArgument */
        self::assertTrue($this->getRemovedImages($uow)->contains($image));
        self::assertArrayHasKey($id->getValue(), $this->getIdentityMap($uow));
    }

    public function testRemoveManagedAndRemoved(): void
    {
        $uow = $this->createUow();

        $id = Id::generate();

        /** @var Image $image */
        $image = $uow->find($id);
        $uow->remove($image);
        $uow->remove($image);

        self::assertCount(0, $this->getNewImages($uow));
        self::assertCount(0, $this->getManagedImages($uow));
        self::assertCount(1, $this->getRemovedImages($uow));
        /** @psalm-suppress InvalidArgument */
        self::assertTrue($this->getRemovedImages($uow)->contains($image));
        self::assertArrayHasKey($id->getValue(), $this->getIdentityMap($uow));
    }

    public function testPersistNewAndRemoved(): void
    {
        $uow = $this->createUow();

        $image = $this->createStub(Image::class);
        $image->method('getId')->willReturn($id = Id::generate());

        $uow->persist($image);
        $uow->remove($image);
        $uow->persist($image);

        self::assertCount(1, $this->getNewImages($uow));
        /** @psalm-suppress InvalidArgument */
        self::assertTrue($this->getNewImages($uow)->contains($image));
        self::assertCount(0, $this->getManagedImages($uow));
        self::assertCount(0, $this->getRemovedImages($uow));
        self::assertArrayHasKey($id->getValue(), $this->getIdentityMap($uow));
    }

    public function testRemoveNewAndRemoved(): void
    {
        $uow = $this->createUow();

        $image = $this->createStub(Image::class);
        $image->method('getId')->willReturn($id = Id::generate());

        $uow->remove($image);
        $uow->remove($image);

        self::assertCount(0, $this->getNewImages($uow));
        self::assertCount(0, $this->getManagedImages($uow));
        self::assertCount(0, $this->getRemovedImages($uow));
        self::assertCount(0, $this->getIdentityMap($uow));
    }

    private function createUow(bool $fileExistence = true): ImageUnitOfWork
    {
        $presetCollection = $this->createStub(PresetCollection::class);

        $localFileHandlerFactory = $this->createStub(LocalFileHandlerFactory::class);
        $localFileHandler = $this->createStub(LocalFileHandler::class);
        $localFileHandlerFactory->method('create')->willReturn($localFileHandler);

        $saverFactory = $this->createStub(SaverFactory::class);
        $saver = $this->createStub(Saver::class);
        $saverFactory->method('create')->willReturn($saver);

        /** @var PathGenerator $pathGenerator */
        $pathGenerator = (new ReflectionClass(PathGenerator::class))->newInstanceWithoutConstructor();
        $baseDirProperty = (new ReflectionClass($pathGenerator))->getProperty('baseDir');
        $baseDirProperty->setAccessible(true);
        $baseDirProperty->setValue($pathGenerator, '/path/to/upload');
        $originalFileNameProperty = (new ReflectionClass($pathGenerator))->getProperty('originalFileName');
        $originalFileNameProperty->setAccessible(true);
        $originalFileNameProperty->setValue($pathGenerator, 'original');

        $fileSystem = $this->createStub(Filesystem::class);
        $fileSystem->method('exists')->willReturn($fileExistence);

        return new ImageUnitOfWork(
            $presetCollection,
            $localFileHandlerFactory,
            $saverFactory,
            $pathGenerator,
            $fileSystem
        );
    }

    private function getNewImages(ImageUnitOfWork $uow): ImageCollection
    {
        /** @var ImageCollection */
        return $this->getValue($uow, 'newImages');
    }

    private function getManagedImages(ImageUnitOfWork $uow): ImageCollection
    {
        /** @var ImageCollection */
        return $this->getValue($uow, 'managedImages');
    }

    private function getRemovedImages(ImageUnitOfWork $uow): ImageCollection
    {
        /** @var ImageCollection */
        return $this->getValue($uow, 'removedImages');
    }

    private function getIdentityMap(ImageUnitOfWork $uow): array
    {
        /** @var array */
        return $this->getValue($uow, 'identityMap');
    }

    /**
     * @return mixed
     */
    private function getValue(ImageUnitOfWork $uow, string $property)
    {
        $property = new ReflectionProperty(ImageUnitOfWork::class, $property);
        $property->setAccessible(true);
        return $property->getValue($uow);
    }
}
