<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Read\PageList;

class Query
{
    public ?int $depth = null;
    public ?string $parentId = null;
    public bool $format = false;
}
