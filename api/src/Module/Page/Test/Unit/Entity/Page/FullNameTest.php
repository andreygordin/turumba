<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Test\Unit\Entity\Page;

use App\Module\Page\Entity\Page\FullName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FullNameTest extends TestCase
{
    public function testSuccess(): void
    {
        $fullName = new FullName($value = 'Полное название');

        self::assertEquals($value, $fullName->getValue());
        self::assertEquals($value, (string)$fullName);
        self::assertFalse($fullName->isEmpty());
    }

    public function testTrim(): void
    {
        $fullName = new FullName(' Полное  название ' . "\t\n");
        self::assertEquals('Полное название', $fullName->getValue());
    }

    public function testLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FullName(str_repeat('а', 256));
    }

    public function testEquality(): void
    {
        $fullName1 = new FullName('Полное название');
        $fullName2 = new FullName('Полное название');

        self::assertTrue($fullName1->isEqualTo($fullName2));
        self::assertTrue($fullName2->isEqualTo($fullName1));

        $fullName3 = new FullName('Другое полное название');

        self::assertFalse($fullName1->isEqualTo($fullName3));
        self::assertFalse($fullName3->isEqualTo($fullName1));
    }
}
