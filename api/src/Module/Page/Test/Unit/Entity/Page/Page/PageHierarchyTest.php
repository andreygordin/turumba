<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Test\Unit\Entity\Page\Page;

use App\Module\Page\Entity\Page\Id;
use App\Module\Page\Entity\Page\Page;
use PHPUnit\Framework\TestCase;

class PageHierarchyTest extends TestCase
{
    public function testCreate(): void
    {
        $page = new Page($id = Id::generate());

        self::assertEquals($id, $page->getId());
    }

    public function testCreateForParent(): void
    {
        $parent = new Page(Id::generate());
        $child = (new Page(Id::generate()))
            ->withParent($parent);

        self::assertTrue($parent->hasChildren());
        self::assertCount(1, $parent->getChildren());
        self::assertEquals($child, $parent->getChildren()[0] ?? null);
        self::assertEquals($parent, $child->getParent());
    }

    public function testChangeParent(): void
    {
        $oldParent = new Page(Id::generate());
        $child = (new Page(Id::generate()))
            ->withParent($oldParent);
        $newParent = new Page(Id::generate());
        $child->setParent($newParent);

        self::assertFalse($oldParent->hasChildren());
        self::assertCount(0, $oldParent->getChildren());
        self::assertTrue($newParent->hasChildren());
        self::assertCount(1, $newParent->getChildren());
        self::assertEquals($child, $newParent->getChildren()[0] ?? null);
        self::assertEquals($newParent, $child->getParent());
    }

    public function testUnsetParent(): void
    {
        $parent = new Page(Id::generate());
        $child = (new Page(Id::generate()))
            ->withParent($parent);
        $child->setParent(null);

        self::assertFalse($parent->hasChildren());
        self::assertCount(0, $parent->getChildren());
        self::assertNull($child->getParent());
    }

    public function testAddRemoveChild(): void
    {
        $parent = new Page(Id::generate());
        $child = new Page(Id::generate());
        $parent->addChild($child);

        self::assertTrue($parent->hasChildren());
        self::assertCount(1, $parent->getChildren());
        self::assertEquals($child, $parent->getChildren()[0] ?? null);
        self::assertEquals($parent, $child->getParent());

        $parent->removeChild($child);

        self::assertFalse($parent->hasChildren());
        self::assertCount(0, $parent->getChildren());
        self::assertNull($child->getParent());
    }

    public function testRestoreDefaults(): void
    {
        $parent = new Page(Id::generate());
        $child = (new Page(Id::generate()))
            ->withParent($parent);

        $child->restoreDefaults();

        self::assertFalse($parent->hasChildren());
        self::assertCount(0, $parent->getChildren());
        self::assertNull($child->getParent());
    }
}
