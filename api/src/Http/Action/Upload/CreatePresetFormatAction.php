<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Http\Action\Upload;

use App\Http\EmptyResponse;
use App\Module\Upload\Command\CreatePresetFormat\Command;
use App\Module\Upload\Command\CreatePresetFormat\Handler;
use App\Module\Upload\Service\AccelPathGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CreatePresetFormatAction implements RequestHandlerInterface
{
    private Handler $handler;
    private AccelPathGenerator $accelPathGenerator;

    public function __construct(Handler $handler, AccelPathGenerator $accelPathGenerator)
    {
        $this->handler = $handler;
        $this->accelPathGenerator = $accelPathGenerator;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $command = new Command();
        $command->id = (string)$request->getAttribute('id');
        $command->preset = (string)$request->getAttribute('preset');
        $command->extension = (string)$request->getAttribute('extension');

        $result = $this->handler->handle($command);

        $accelPath = $this->accelPathGenerator
            ->getFilePath($result->getId(), $result->getExtension(), $result->getPreset());

        /** @var EmptyResponse $response */
        $response = (new EmptyResponse(201))
            ->withHeader('Content-Type', $result->getMimeType())
            ->withHeader('X-Accel-Redirect', $accelPath);

        return $response;
    }
}
