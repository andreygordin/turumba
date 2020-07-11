<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service\FileHandler;

use GuzzleHttp\Client;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

class RemoteFileHandler implements FileHandlerInterface
{
    private string $url;
    private Client $client;

    private ?string $mimeType = null;
    private ?StreamInterface $stream = null;

    public function __construct(string $url, Client $client)
    {
        if (parse_url($url) === false) {
            throw new InvalidArgumentException('Invalid URL');
        }
        $this->url = $url;
        $this->client = $client;
    }

    public function getMimeType(): string
    {
        if ($this->mimeType === null) {
            $mimeType = $this->client
                ->head($this->url)
                ->getHeaderLine('content-type');
            if (empty($mimeType)) {
                throw new MimeTypeException();
            }
            $this->mimeType = $mimeType;
        }
        return $this->mimeType;
    }

    public function getStream(): StreamInterface
    {
        if ($this->stream === null) {
            $this->stream = $this->client->get($this->url)->getBody();
        }
        return $this->stream;
    }
}
