<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service\FileHandler;

use finfo;
use InvalidArgumentException;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Webmozart\Assert\Assert;

class LocalFileHandler implements FileHandlerInterface
{
    private string $path;
    private StreamFactoryInterface $streamFactory;

    private ?string $mimeType = null;
    private ?StreamInterface $stream = null;

    public function __construct(string $path, StreamFactoryInterface $streamFactory)
    {
        Assert::notEmpty($path);
        if (!file_exists($path)) {
            throw new InvalidArgumentException('File doesnt\'t exist');
        }
        $this->path = $path;
        $this->streamFactory = $streamFactory;
    }

    public function getMimeType(): string
    {
        if ($this->mimeType === null) {
            $mimeType = (new finfo(FILEINFO_MIME_TYPE))->file($this->path);
            if ($mimeType === false) {
                throw new MimeTypeException();
            }
            $this->mimeType = $mimeType;
        }
        return $this->mimeType;
    }

    public function getStream(): StreamInterface
    {
        if ($this->stream === null) {
            $this->stream = $this->streamFactory->createStreamFromFile($this->path, 'r');
        }
        return $this->stream;
    }
}
