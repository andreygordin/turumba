<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service;

use finfo;
use Psr\Http\Message\StreamInterface;
use UnexpectedValueException;

class MimeTypeValidator
{
    public function validate(StreamInterface $stream, string $expectedMimeType): void
    {
        $streamMimeType = (new finfo(FILEINFO_MIME_TYPE))->buffer($stream->getContents());
        if ($streamMimeType !== $expectedMimeType) {
            throw new UnexpectedValueException(
                'Stream mime type is ' . $streamMimeType . '. Was expecting ' . $expectedMimeType . '.'
            );
        }
    }
}
