<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Command\Update;

use App\Exception\DomainNotFoundException;
use App\Flusher;
use App\Module\Page\Command\Update\Command;
use App\Module\Page\Entity\Page\FullName;
use App\Module\Page\Entity\Page\Id;
use App\Module\Page\Entity\Page\Note;
use App\Module\Page\Entity\Page\PageRepository;
use App\Module\Page\Entity\Page\Path;
use App\Module\Page\Entity\Page\ShortName;
use App\Module\Page\Entity\Page\Title;
use DomainException;

class Handler
{
    private PageRepository $pages;
    private Flusher $flusher;

    public function __construct(PageRepository $pages, Flusher $flusher)
    {
        $this->pages = $pages;
        $this->flusher = $flusher;
    }

    public function handle(Command\Page $command): void
    {
        $id = new Id($command->id);

        $page = $this->pages->find($id);
        if (!$page) {
            throw new DomainNotFoundException();
        }

        $page->restoreDefaults();

        if ($command->parentId !== null) {
            $parent = $this->pages->get(new Id($command->parentId));
            $page->setParent($parent);
        }

        if ($command->shortName !== null) {
            $page->setShortName(new ShortName($command->shortName));
        }

        if ($command->fullName !== null) {
            $page->setFullName(new FullName($command->fullName));
        }

        if ($command->title !== null) {
            $page->setTitle(new Title($command->title));
        }

        if ($command->path !== null) {
            $path = new Path($command->path->slug1, $command->path->slug2, $command->path->slug3);
            if ($this->pages->existsByPath($path, [$id])) {
                throw new DomainException('Page with this path already exists.');
            }
            $page->setPath($path);
        }

        if ($command->isVisible !== null) {
            $page->setVisibility($command->isVisible);
        }

        if ($command->note !== null) {
            $page->setNote(new Note($command->note));
        }

        $this->flusher->flush();
    }
}
