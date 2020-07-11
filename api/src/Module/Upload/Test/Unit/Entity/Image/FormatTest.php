<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Test\Unit\Entity\Image;

use App\Module\Upload\Entity\Image\Format;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FormatTest extends TestCase
{
    public function testDefaultCreation(): void
    {
        $format = new Format(Format::JPEG);
        self::assertEquals(Format::JPEG, $format->getValue());
        self::assertEquals('jpg', $format->getExtension());
        self::assertEquals('image/jpeg', $format->getMimeType());

        $format = new Format(Format::PNG);
        self::assertEquals(Format::PNG, $format->getValue());
        self::assertEquals('png', $format->getExtension());
        self::assertEquals('image/png', $format->getMimeType());

        $format = new Format(Format::WEBP);
        self::assertEquals(Format::WEBP, $format->getValue());
        self::assertEquals('webp', $format->getExtension());
        self::assertEquals('image/webp', $format->getMimeType());
    }

    public function testFactoryCreation(): void
    {
        $format = Format::jpeg();
        self::assertEquals(Format::JPEG, $format->getValue());
        self::assertEquals('jpg', $format->getExtension());
        self::assertEquals('image/jpeg', $format->getMimeType());

        $format = Format::png();
        self::assertEquals(Format::PNG, $format->getValue());
        self::assertEquals('png', $format->getExtension());
        self::assertEquals('image/png', $format->getMimeType());

        $format = Format::webp();
        self::assertEquals(Format::WEBP, $format->getValue());
        self::assertEquals('webp', $format->getExtension());
        self::assertEquals('image/webp', $format->getMimeType());
    }

    public function testCreationForExtension(): void
    {
        $format = Format::createForExtension('jpg');
        self::assertEquals(Format::JPEG, $format->getValue());
        self::assertEquals('jpg', $format->getExtension());
        self::assertEquals('image/jpeg', $format->getMimeType());

        $format = Format::createForExtension('png');
        self::assertEquals(Format::PNG, $format->getValue());
        self::assertEquals('png', $format->getExtension());
        self::assertEquals('image/png', $format->getMimeType());

        $format = Format::createForExtension('webp');
        self::assertEquals(Format::WEBP, $format->getValue());
        self::assertEquals('webp', $format->getExtension());
        self::assertEquals('image/webp', $format->getMimeType());
    }

    public function testInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Format(123);
    }

    public function testEquality(): void
    {
        $format1 = Format::jpeg();
        $format2 = Format::jpeg();

        self::assertTrue($format1->isEqualTo($format2));
        self::assertTrue($format2->isEqualTo($format1));

        $format3 = Format::png();

        self::assertFalse($format1->isEqualTo($format3));
        self::assertFalse($format3->isEqualTo($format1));
    }
}
