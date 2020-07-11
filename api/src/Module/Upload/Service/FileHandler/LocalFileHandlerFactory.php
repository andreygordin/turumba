<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service\FileHandler;

use Psr\Http\Message\StreamFactoryInterface;

class LocalFileHandlerFactory
{
    private StreamFactoryInterface $streamFactory;

    public function __construct(StreamFactoryInterface $streamFactory)
    {
        $this->streamFactory = $streamFactory;
    }

    public function create(string $path): LocalFileHandler
    {
        return new LocalFileHandler($path, $this->streamFactory);
    }
}
