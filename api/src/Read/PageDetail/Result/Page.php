<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Read\PageDetail\Result;

/**
 * @psalm-suppress MissingConstructor
 */
class Page
{
    public string $id = '';
    public ?string $fullName = null;
    public bool $isVisible;
    public ?string $note = null;
    public ?string $parentId = null;
    public Path $path;
    public ?string $shortName = null;
    public ?string $title = null;
}
