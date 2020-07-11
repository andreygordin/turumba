<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service\FileHandler;

use Psr\Http\Message\StreamInterface;

interface FileHandlerInterface
{
    /**
     * @throws MimeTypeException
     */
    public function getMimeType(): string;

    public function getStream(): StreamInterface;
}
