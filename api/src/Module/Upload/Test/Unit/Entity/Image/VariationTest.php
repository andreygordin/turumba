<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Test\Unit\Entity\Image;

use App\Module\Upload\Entity\Image\Format;
use App\Module\Upload\Entity\Image\Preset;
use App\Module\Upload\Entity\Image\Variation;
use DomainException;
use PHPUnit\Framework\TestCase;

class VariationTest extends TestCase
{
    public function testDefault(): void
    {
        $preset = new Preset(
            function () {
            }
        );

        $variation = new Variation($preset);

        self::assertEquals($preset, $variation->getPreset());
    }

    public function testFormats(): void
    {
        $preset = new Preset(
            function () {
            }
        );

        $variation = new Variation($preset);

        self::assertFalse($variation->hasFormats());
        self::assertCount(0, $variation->getFormats());

        self::assertFalse($variation->hasFormat(Format::jpeg()));

        $variation->addFormat(Format::jpeg());

        self::assertTrue($variation->hasFormats());
        self::assertCount(1, $variation->getFormats());

        $format = $variation->getFormats()[0] ?? null;
        self::assertNotNull($format);
        self::assertEquals(Format::JPEG, $format->getValue());

        self::assertTrue($variation->hasFormat(Format::jpeg()));

        // Cannot create the same format twice
        $this->expectException(DomainException::class);
        $variation->addFormat(Format::jpeg());

        $variation->removeFormat(Format::jpeg());
        self::assertFalse($variation->hasFormats());
        self::assertCount(0, $variation->getFormats());
    }
}
