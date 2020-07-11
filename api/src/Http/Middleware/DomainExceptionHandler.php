<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Exception\DomainNotFoundException;
use App\Http\JsonResponse;
use DomainException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class DomainExceptionHandler implements MiddlewareInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (DomainNotFoundException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e, 'url' => (string)$request->getUri()]);
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (DomainException $e) {
            $this->logger->warning($e->getMessage(), ['exception' => $e, 'url' => (string)$request->getUri()]);
            return new JsonResponse(['message' => $e->getMessage()], 409);
        }
    }
}
