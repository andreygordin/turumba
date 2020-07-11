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

class Saver
{
    private StreamInterface $stream;
    private MimeTypeValidator $mimeTypeValidator;
    private StreamFactoryInterface $streamFactory;
    private ?StreamHandlerInterface $streamHandler;

    public function __construct(
        StreamInterface $stream,
        MimeTypeValidator $mimeTypeValidator,
        StreamFactoryInterface $streamFactory,
        ?StreamHandlerInterface $streamHandler = null
    ) {
        $this->stream = $stream;
        $this->mimeTypeValidator = $mimeTypeValidator;
        $this->streamFactory = $streamFactory;
        $this->streamHandler = $streamHandler;
    }

    public function save(string $targetPath, string $expectedMimeType, ?callable $streamHandlerCallback = null): void
    {
        $readStream = clone $this->stream;
        if ($this->streamHandler !== null) {
            $streamHandler = clone $this->streamHandler;
            if ($streamHandlerCallback !== null) {
                call_user_func($streamHandlerCallback, $streamHandler);
            }
            $streamHandler->handle($readStream, $expectedMimeType);
            $readStream->rewind();
        }

        $this->mimeTypeValidator->validate($readStream, $expectedMimeType);
        $readStream->rewind();

        $writeStream = $this->streamFactory->createStreamFromFile($targetPath, 'w');
        $writeStream->write($readStream->getContents());
    }
}
