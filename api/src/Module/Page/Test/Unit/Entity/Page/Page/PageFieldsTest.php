<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Test\Unit\Entity\Page\Page;

use App\Module\Page\Entity\Page\FullName;
use App\Module\Page\Entity\Page\Id;
use App\Module\Page\Entity\Page\Note;
use App\Module\Page\Entity\Page\Page;
use App\Module\Page\Entity\Page\Path;
use App\Module\Page\Entity\Page\ShortName;
use App\Module\Page\Entity\Page\Title;
use DomainException;
use PHPUnit\Framework\TestCase;

class PageFieldsTest extends TestCase
{
    public function testDefault(): void
    {
        $page = (new Page(Id::generate()));

        self::assertInstanceOf(ShortName::class, $page->getShortName());
        self::assertEquals('', $page->getShortName()->getValue());
        self::assertTrue($page->getShortName()->isEmpty());
        self::assertInstanceOf(FullName::class, $page->getFullName());
        self::assertEquals('', $page->getFullName()->getValue());
        self::assertTrue($page->getFullName()->isEmpty());
        self::assertInstanceOf(Title::class, $page->getTitle());
        self::assertEquals('', $page->getTitle()->getValue());
        self::assertTrue($page->getTitle()->isEmpty());
        self::assertInstanceOf(Path::class, $page->getPath());
        self::assertEquals('', $page->getPath()->getSlug1());
        self::assertEquals('', $page->getPath()->getSlug2());
        self::assertEquals('', $page->getPath()->getSlug3());
        self::assertFalse($page->isVisible());
        self::assertInstanceOf(Note::class, $page->getNote());
        self::assertEquals('', $page->getNote()->getValue());
        self::assertTrue($page->getNote()->isEmpty());
    }

    public function testFillOnCreation(): void
    {
        $page = (new Page($id = Id::generate()))
            ->withShortName($shortName = new ShortName('Краткое название'))
            ->withFullName($fullName = new FullName('Полное название'))
            ->withTitle($title = new Title('Заголовок'))
            ->withPath($path = new Path('slug1', 'slug2', 'slug3'))
            ->withVisibility(false)
            ->withNote($note = new Note('Заметка'));

        self::assertEquals($id, $page->getId());
        self::assertEquals($shortName, $page->getShortName());
        self::assertEquals($fullName, $page->getFullName());
        self::assertEquals($title, $page->getTitle());
        self::assertEquals($path, $page->getPath());
        self::assertFalse($page->isVisible());
        self::assertEquals($note, $page->getNote());
    }

    public function testUpdate(): void
    {
        $page = new Page(Id::generate());
        $page->setShortName($shortName = new ShortName('Краткое название'));
        $page->setFullName($fullName = new FullName('Полное название'));
        $page->setTitle($title = new Title('Заголовок'));
        $page->setPath($path = new Path('slug1', 'slug2', 'slug3'));
        $page->setVisibility(false);
        $page->setNote($note = new Note('Заметка'));

        self::assertEquals($shortName, $page->getShortName());
        self::assertEquals($fullName, $page->getFullName());
        self::assertEquals($title, $page->getTitle());
        self::assertEquals($path, $page->getPath());
        self::assertFalse($page->isVisible());
        self::assertEquals($note, $page->getNote());
    }

    public function testRestoreDefaults(): void
    {
        $page = (new Page($id = Id::generate()))
            ->withShortName(new ShortName('Краткое название'))
            ->withFullName(new FullName('Полное название'))
            ->withTitle(new Title('Заголовок'))
            ->withPath(new Path('slug1', 'slug2', 'slug3'))
            ->withVisibility(true)
            ->withNote(new Note('Заметка'));

        $page->restoreDefaults();

        self::assertEquals($id, $page->getId());
        self::assertEquals('', $page->getShortName()->getValue());
        self::assertEquals('', $page->getFullName()->getValue());
        self::assertEquals('', $page->getTitle()->getValue());
        self::assertEquals('', $page->getPath()->getSlug1());
        self::assertEquals('', $page->getPath()->getSlug2());
        self::assertEquals('', $page->getPath()->getSlug3());
        self::assertFalse($page->isVisible());
        self::assertEquals('', $page->getNote()->getValue());
    }

    public function testValidation(): void
    {
        $page = new Page(Id::generate());

        $this->expectException(DomainException::class);
        $page->setVisibility(true);

        self::assertFalse($page->isValid());
        $page->setShortName($shortName = new ShortName('Краткое название'));
        self::assertFalse($page->isValid());
        $page->setFullName($fullName = new FullName('Полное название'));
        self::assertFalse($page->isValid());
        $page->setTitle($title = new Title('Заголовок'));
        self::assertTrue($page->isValid());

        $page->setVisibility(true);
    }
}
