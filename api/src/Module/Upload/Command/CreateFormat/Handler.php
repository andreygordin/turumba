<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Command\CreateFormat;

use App\Exception\DomainNotFoundException;
use App\Module\Upload\Entity\Image\Format;
use App\Module\Upload\Entity\Image\Id;
use App\Module\Upload\Entity\Image\ImageRepository;

class Handler
{
    private ImageRepository $images;

    public function __construct(ImageRepository $images)
    {
        $this->images = $images;
    }

    public function handle(Command $command): Result
    {
        $id = new Id($command->id);

        $image = $this->images->find($id);
        if (!$image) {
            throw new DomainNotFoundException();
        }

        $format = Format::createForExtension($command->extension);
        $image->addFormat($format);

        $this->images->flush();

        return new Result(
            $image->getId()->getValue(),
            $format->getExtension(),
            $format->getMimeType()
        );
    }
}
