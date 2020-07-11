<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Test\Unit\Entity\Page;

use App\Module\Page\Entity\Page\Note;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class NoteTest extends TestCase
{
    public function testSuccess(): void
    {
        $note = new Note($value = 'Заметка');

        self::assertEquals($value, $note->getValue());
        self::assertEquals($value, (string)$note);
        self::assertFalse($note->isEmpty());
    }

    public function testTrim(): void
    {
        $note = new Note(' Заметка ' . "\t\n");
        self::assertEquals('Заметка', $note->getValue());
    }

    public function testLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Note(str_repeat('а', 65536));
    }

    public function testEquality(): void
    {
        $note1 = new Note('Заметка');
        $note2 = new Note('Заметка');

        self::assertTrue($note1->isEqualTo($note2));
        self::assertTrue($note2->isEqualTo($note1));

        $note3 = new Note('Другая заметка');

        self::assertFalse($note1->isEqualTo($note3));
        self::assertFalse($note3->isEqualTo($note1));
    }
}
