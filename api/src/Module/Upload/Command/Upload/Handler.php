<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Command\Upload;

use App\Module\Upload\Entity\Image\Format;
use App\Module\Upload\Entity\Image\Id;
use App\Module\Upload\Entity\Image\Image;
use App\Module\Upload\Entity\Image\ImageRepository;
use App\Module\Upload\Service\FileHandler\MimeTypeException;
use App\Module\Upload\Service\FileHandler\UploadedFileHandlerFactory;
use Webmozart\Assert\Assert;

class Handler
{
    private ImageRepository $images;
    private UploadedFileHandlerFactory $fileHandlerFactory;

    public function __construct(ImageRepository $images, UploadedFileHandlerFactory $fileHandlerFactory)
    {
        $this->images = $images;
        $this->fileHandlerFactory = $fileHandlerFactory;
    }

    public function handle(Command $command): Result
    {
        Assert::notNull($command->file);

        $fileHandler = $this->fileHandlerFactory->create($command->file);

        try {
            $format = $fileHandler->getMimeType() === 'image/png' ? Format::png() : Format::jpeg();
        } catch (MimeTypeException $e) {
            $format = Format::jpeg();
        }

        $image = new Image(Id::generate(), $format, $fileHandler);
        $this->images->add($image);
        $this->images->flush();

        return new Result(
            $image->getId()->getValue(),
            $image->getOriginalFormat()->getExtension()
        );
    }
}
