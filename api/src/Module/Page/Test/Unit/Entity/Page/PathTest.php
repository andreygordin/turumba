<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Test\Unit\Entity\Page;

use App\Module\Page\Entity\Page\Path;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    public function testSuccess(): void
    {
        $path = new Path('slug1', 'slug2', 'slug3');

        self::assertEquals('slug1', $path->getSlug1());
        self::assertEquals('slug2', $path->getSlug2());
        self::assertEquals('slug3', $path->getSlug3());
        self::assertEquals('slug1/slug2/slug3', $path->getValue());
        self::assertEquals('slug1/slug2/slug3', (string)$path);
    }

    public function testCase(): void
    {
        $path = new Path('SLUG1', 'Slug2', 'sLuG3');

        self::assertEquals('slug1', $path->getSlug1());
        self::assertEquals('slug2', $path->getSlug2());
        self::assertEquals('slug3', $path->getSlug3());
    }

    public function testSymbols(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Path('слаг1', '', '');

        $this->expectException(InvalidArgumentException::class);
        new Path('slug_1', '', '');

        $this->expectException(InvalidArgumentException::class);
        new Path('slug 1', '', '');

        $this->expectException(InvalidArgumentException::class);
        new Path('slug1/slug2', '', '');
    }

    public function testTrim(): void
    {
        $path = new Path(' slug1 ' . "\t\n", 'slug2 ', '  ');
        self::assertEquals('slug1', $path->getSlug1());
        self::assertEquals('slug2', $path->getSlug2());
        self::assertEquals('', $path->getSlug3());
    }


    public function testEmpty(): void
    {
        new Path('', '', '');
        new Path('slug1', '', '');

        $this->expectException(InvalidArgumentException::class);
        new Path('', 'slug2', '');

        $this->expectException(InvalidArgumentException::class);
        new Path('slug1', '', 'slug3');
    }

    public function testLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Path(str_repeat('а', 256), '', '');

        $this->expectException(InvalidArgumentException::class);
        new Path('', str_repeat('а', 256), '');

        $this->expectException(InvalidArgumentException::class);
        new Path('', '', str_repeat('а', 256));
    }

    public function testEquality(): void
    {
        $path1 = new Path('slug1', 'slug2', 'slug3');
        $path2 = new Path('slug1', 'slug2', 'slug3');

        self::assertTrue($path1->isEqualTo($path2));
        self::assertTrue($path2->isEqualTo($path1));

        $path3 = new Path('slug1', 'slug2', 'different-slug3');

        self::assertFalse($path1->isEqualTo($path3));
        self::assertFalse($path3->isEqualTo($path1));
    }
}
