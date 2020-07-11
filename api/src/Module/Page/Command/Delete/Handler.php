<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Command\Delete;

use App\Exception\DomainNotFoundException;
use App\Flusher;
use App\Module\Page\Entity\Page\Id;
use App\Module\Page\Entity\Page\PageRepository;

class Handler
{
    private PageRepository $pages;
    private Flusher $flusher;

    public function __construct(PageRepository $pages, Flusher $flusher)
    {
        $this->pages = $pages;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void
    {
        $page = $this->pages->find(new Id($command->id));
        if (!$page) {
            throw new DomainNotFoundException();
        }

        $this->pages->remove($page);

        $this->flusher->flush();
    }
}
