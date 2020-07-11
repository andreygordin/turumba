<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Entity\Image;

use Arrayy\Collection\AbstractCollection;

class ImageCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Image::class;
    }

    public function findById(Id $id): ?Image
    {
        /** @var Image|false $result */
        $result = $this->find(
            function (Image $image) use ($id) {
                return $image->getId()->isEqualTo($id);
            }
        );
        return $result ?: null;
    }
}
