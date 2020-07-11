<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service\FileHandler;

use GuzzleHttp\Client;

class RemoteFileHandlerFactory
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function create(string $url): RemoteFileHandler
    {
        return new RemoteFileHandler($url, $this->client);
    }
}
