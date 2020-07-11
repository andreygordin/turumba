<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Test\Unit\Entity\Page;

use App\Module\Page\Entity\Page\ShortName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ShortNameTest extends TestCase
{
    public function testSuccess(): void
    {
        $shortName = new ShortName($value = 'Краткое название');

        self::assertEquals($value, $shortName->getValue());
        self::assertEquals($value, (string)$shortName);
        self::assertFalse($shortName->isEmpty());
    }

    public function testTrim(): void
    {
        $shortName = new ShortName(' Краткое  название ' . "\t\n");
        self::assertEquals('Краткое название', $shortName->getValue());
    }

    public function testLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ShortName(str_repeat('а', 256));
    }

    public function testEquality(): void
    {
        $shortName1 = new ShortName('Краткое название');
        $shortName2 = new ShortName('Краткое название');

        self::assertTrue($shortName1->isEqualTo($shortName2));
        self::assertTrue($shortName2->isEqualTo($shortName1));

        $shortName3 = new ShortName('Другое краткое название');

        self::assertFalse($shortName1->isEqualTo($shortName3));
        self::assertFalse($shortName3->isEqualTo($shortName1));
    }
}
