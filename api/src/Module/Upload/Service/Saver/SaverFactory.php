<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service\Saver;

use App\Module\Upload\Service\MimeTypeValidator;
use App\Module\Upload\Service\StreamHandler\StreamHandlerInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class SaverFactory
{
    private MimeTypeValidator $mimeTypeValidator;
    private StreamFactoryInterface $streamFactory;
    private ?StreamHandlerInterface $streamHandler;

    public function __construct(
        MimeTypeValidator $mimeTypeValidator,
        StreamFactoryInterface $streamFactory,
        ?StreamHandlerInterface $streamHandler = null
    ) {
        $this->mimeTypeValidator = $mimeTypeValidator;
        $this->streamFactory = $streamFactory;
        $this->streamHandler = $streamHandler;
    }

    public function create(StreamInterface $stream): Saver
    {
        return new Saver($stream, $this->mimeTypeValidator, $this->streamFactory, $this->streamHandler);
    }
}
