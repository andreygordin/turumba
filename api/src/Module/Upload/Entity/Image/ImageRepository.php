<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Entity\Image;

use DomainException;

class ImageRepository
{
    private ImageUnitOfWork $uow;

    public function __construct(ImageUnitOfWork $uow)
    {
        $this->uow = $uow;
    }

    public function get(Id $id): Image
    {
        if (!$image = $this->find($id)) {
            throw new DomainException('Image not found.');
        }
        /** @var Image $image */
        return $image;
    }

    public function find(Id $id): ?Image
    {
        return $this->uow->find($id);
    }

    public function exists(Id $id): bool
    {
        return $this->uow->exists($id);
    }

    public function add(Image $image): void
    {
        $this->uow->persist($image);
    }

    public function remove(Image $image): void
    {
        $this->uow->remove($image);
    }

    public function flush(): void
    {
        $this->uow->commit();
    }
}
