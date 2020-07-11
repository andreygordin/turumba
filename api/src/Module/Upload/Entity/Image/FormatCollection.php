<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Entity\Image;

use Arrayy\Collection\AbstractCollection;

class FormatCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Format::class;
    }
}
