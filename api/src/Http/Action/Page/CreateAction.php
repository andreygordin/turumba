<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Http\Action\Page;

use App\Http\EmptyResponse;
use App\Http\Validator\Validator;
use App\Module\Page\Command\Create\Command\Page;
use App\Module\Page\Command\Create\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CreateAction implements RequestHandlerInterface
{
    private Handler $handler;
    private DenormalizerInterface $denormalizer;
    private Validator $validator;

    public function __construct(Handler $handler, DenormalizerInterface $denormalizer, Validator $validator)
    {
        $this->handler = $handler;
        $this->denormalizer = $denormalizer;
        $this->validator = $validator;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getParsedBody();

        /** @var Page $command */
        $command = $this->denormalizer->denormalize($data, Page::class);

        $this->validator->validate($command);

        $result = $this->handler->handle($command);

        $location = '/pages/' . $result->getId();
        /** @var EmptyResponse $response */
        $response = (new EmptyResponse(201))
            ->withHeader('Location', $location);

        return $response;
    }
}
