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
use App\Module\Page\Command\Patch\Command\Page;
use App\Module\Page\Command\Patch\Handler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PatchAction implements RequestHandlerInterface
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
        $data = (array)$request->getParsedBody();
        $data['id'] = (string)$request->getAttribute('id');

        /** @var Page $command */
        $command = $this->denormalizer->denormalize($data, Page::class);

        $this->validator->validate($command);

        $this->handler->handle($command);

        $response = new EmptyResponse();

        return $response;
    }
}
