<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service\FileHandler;

use Psr\Http\Message\UploadedFileInterface;

class UploadedFileHandlerFactory
{
    public function create(UploadedFileInterface $uploadedFile): UploadedFileHandler
    {
        return new UploadedFileHandler($uploadedFile);
    }
}
