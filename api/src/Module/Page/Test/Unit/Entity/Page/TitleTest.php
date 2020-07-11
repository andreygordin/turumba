<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Test\Unit\Entity\Page;

use App\Module\Page\Entity\Page\Title;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TitleTest extends TestCase
{
    public function testSuccess(): void
    {
        $title = new Title($value = 'Заголовок');

        self::assertEquals($value, $title->getValue());
        self::assertEquals($value, (string)$title);
        self::assertFalse($title->isEmpty());
    }

    public function testTrim(): void
    {
        $title = new Title(' Мой  заголовок ' . "\t\n");
        self::assertEquals('Мой заголовок', $title->getValue());
    }

    public function testLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Title(str_repeat('а', 256));
    }

    public function testEquality(): void
    {
        $title1 = new Title('Заголовок');
        $title2 = new Title('Заголовок');

        self::assertTrue($title1->isEqualTo($title2));
        self::assertTrue($title2->isEqualTo($title1));

        $title3 = new Title('Другой заголовок');

        self::assertFalse($title1->isEqualTo($title3));
        self::assertFalse($title3->isEqualTo($title1));
    }
}
