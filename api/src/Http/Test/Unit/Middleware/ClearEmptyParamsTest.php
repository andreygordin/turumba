<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Http\Test\Unit\Middleware;

use App\Http\Middleware\ClearEmptyParams;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionMethod;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;

class ClearEmptyParamsTest extends TestCase
{
    public function testNormal(): void
    {
        $middleware = new ClearEmptyParams();

        $handler = $this->createStub(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn($source = (new ResponseFactory())->createResponse());

        $request = (new ServerRequestFactory())->createServerRequest('GET', 'http://test');
        $response = $middleware->process($request, $handler);

        self::assertEquals($source, $response);
    }

    public function testFiltering(): void
    {
        $middleware = $this->createMock(ClearEmptyParams::class);
        $reflectedMethod = new ReflectionMethod(ClearEmptyParams::class, 'filterParams');
        $reflectedMethod->setAccessible(true);

        $params = [
            'emptyString' => '',
            'nonEmptyString' => 'nonEmptyString',
            'space' => ' ',
            'zeroInt' => 0,
            'zeroFloat' => 0.0,
            'false' => false,
            'null' => null,
        ];

        /** @var array $filteredParams */
        $filteredParams = $reflectedMethod->invokeArgs($middleware, [$params]);

        self::assertEquals(
            [
                'nonEmptyString' => 'nonEmptyString',
                'space' => ' ',
                'zeroInt' => 0,
                'zeroFloat' => 0.0,
                'false' => false,
                'null' => null,
            ],
            $filteredParams
        );
    }
}
