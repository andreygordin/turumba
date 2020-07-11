<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Read\PageList\Result;

use Arrayy\Collection\AbstractCollection;

class PageCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Page::class;
    }
}
