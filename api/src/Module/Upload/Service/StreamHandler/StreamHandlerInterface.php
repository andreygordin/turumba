<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service\StreamHandler;

use Psr\Http\Message\StreamInterface;

interface StreamHandlerInterface
{
    public function handle(StreamInterface &$stream, string $expectedMimeType): void;
}
