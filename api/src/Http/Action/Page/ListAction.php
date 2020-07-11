<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Http\Action\Page;

use App\Http\JsonResponse;
use App\Read\PageList\Handler;
use App\Read\PageList\Query;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ListAction implements RequestHandlerInterface
{
    private Handler $handler;
    private NormalizerInterface $normalizer;
    private DenormalizerInterface $denormalizer;

    public function __construct(Handler $handler, NormalizerInterface $normalizer, DenormalizerInterface $denormalizer)
    {
        $this->handler = $handler;
        $this->normalizer = $normalizer;
        $this->denormalizer = $denormalizer;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var Query $query */
        $query = $this->denormalizer->denormalize($request->getQueryParams(), Query::class);

        $response = $this->handler
            ->withQuery($query)
            ->handle();

        $data = $this->normalizer->normalize($response);

        return new JsonResponse($data);
    }
}
