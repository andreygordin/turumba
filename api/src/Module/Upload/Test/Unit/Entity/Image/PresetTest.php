<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Test\Unit\Entity\Image;

use App\Module\Upload\Entity\Image\Preset;
use PHPUnit\Framework\TestCase;

class PresetTest extends TestCase
{
    public function testDefault(): void
    {
        $handler = function (): void {
        };

        $preset = new Preset($handler);

        self::assertEquals($handler, $preset->getHandler());
    }
}
