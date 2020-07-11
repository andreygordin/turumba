<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service\FileHandler;

use LogicException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;

class UploadedFileHandler implements FileHandlerInterface
{
    private UploadedFileInterface $source;

    public function __construct(UploadedFileInterface $source)
    {
        if ($source->getError() !== UPLOAD_ERR_OK) {
            throw new LogicException('File not uploaded. Error code: ' . $source->getError() . '.');
        }
        $this->source = $source;
    }

    public function getMimeType(): string
    {
        $mimeType = $this->source->getClientMediaType();
        if ($mimeType === null) {
            throw new MimeTypeException();
        }
        return $mimeType;
    }

    public function getStream(): StreamInterface
    {
        return $this->source->getStream();
    }
}
