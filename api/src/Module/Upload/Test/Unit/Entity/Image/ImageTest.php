<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Test\Unit\Entity\Image;

use App\Module\Upload\Entity\Image\Format;
use App\Module\Upload\Entity\Image\Id;
use App\Module\Upload\Entity\Image\Image;
use App\Module\Upload\Entity\Image\Preset;
use App\Module\Upload\Service\FileHandler\FileHandlerInterface;
use DomainException;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    public function testDefault(): void
    {
        $fileHandler = $this->createStub(FileHandlerInterface::class);
        $image = new Image($id = Id::generate(), $originalFormat = Format::jpeg(), $fileHandler);

        self::assertEquals($id, $image->getId());
        self::assertEquals($originalFormat, $image->getOriginalFormat());
        self::assertEquals($fileHandler, $image->getFileHandler());
    }

    public function testSwitchFileHandler(): void
    {
        $oldFileHandler = $this->createStub(FileHandlerInterface::class);
        $image = new Image(Id::generate(), Format::jpeg(), $oldFileHandler);

        $newFileHandler = $this->createStub(FileHandlerInterface::class);
        $image->setFileHandler($newFileHandler);

        self::assertEquals($newFileHandler, $image->getFileHandler());
    }

    public function testFormats(): void
    {
        $fileHandler = $this->createStub(FileHandlerInterface::class);
        $image = new Image(Id::generate(), Format::jpeg(), $fileHandler);

        self::assertFalse($image->hasFormats());
        self::assertCount(0, $image->getFormats());

        self::assertFalse($image->hasFormat(Format::jpeg()));

        $image->addFormat(Format::jpeg());

        self::assertTrue($image->hasFormats());
        self::assertCount(1, $image->getFormats());

        $format = $image->getFormats()[0] ?? null;
        self::assertNotNull($format);
        self::assertEquals(Format::JPEG, $format->getValue());

        self::assertTrue($image->hasFormat(Format::jpeg()));

        // Cannot create the same format twice
        $this->expectException(DomainException::class);
        $image->addFormat(Format::jpeg());

        $image->removeFormat(Format::jpeg());
        self::assertFalse($image->hasFormats());
        self::assertCount(0, $image->getFormats());
    }

    public function testVariations(): void
    {
        $fileHandler = $this->createStub(FileHandlerInterface::class);
        $image = new Image(Id::generate(), Format::jpeg(), $fileHandler);

        self::assertFalse($image->hasVariations());
        self::assertCount(0, $image->getVariations());

        $preset = new Preset(
            function () {
            }
        );

        self::assertFalse($image->hasVariation($preset));
        self::assertNull($image->getVariation($preset));

        $image->createVariation($preset);

        self::assertTrue($image->hasVariations());
        self::assertCount(1, $image->getVariations());

        $variation = $image->getVariations()[0] ?? null;
        self::assertNotNull($variation);
        self::assertEquals($preset, $variation->getPreset());

        self::assertTrue($image->hasVariation($preset));
        self::assertEquals($variation, $image->getVariation($preset));

        // Cannot create variation for the same preset
        $this->expectException(DomainException::class);
        $image->createVariation($preset);

        $image->removeVariation($variation);
        self::assertFalse($image->hasVariations());
        self::assertCount(0, $image->getVariations());
    }
}
