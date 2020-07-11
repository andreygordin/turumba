<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Http\Action\Upload;

use App\Http\EmptyResponse;
use App\Http\Validator\Validator;
use App\Module\Upload\Command\Upload\Command;
use App\Module\Upload\Command\Upload\Handler;
use App\Module\Upload\Service\UrlGenerator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class UploadAction implements RequestHandlerInterface
{
    private Handler $handler;
    private DenormalizerInterface $denormalizer;
    private Validator $validator;
    private UrlGenerator $urlGenerator;

    public function __construct(
        Handler $handler,
        DenormalizerInterface $denormalizer,
        Validator $validator,
        UrlGenerator $urlGenerator
    ) {
        $this->handler = $handler;
        $this->denormalizer = $denormalizer;
        $this->validator = $validator;
        $this->urlGenerator = $urlGenerator;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $data = $request->getUploadedFiles();

        /** @var Command $command */
        $command = $this->denormalizer->denormalize($data, Command::class);

        $this->validator->validate($command);

        $result = $this->handler->handle($command);

        $location = $this->urlGenerator->getAbsoluteUrl($result->getId(), 'image', $result->getExtension());

        /** @var EmptyResponse $response */
        $response = (new EmptyResponse(201))
            ->withHeader('Location', $location);

        return $response;
    }
}
