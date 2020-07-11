<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Http\Test\Unit;

use App\Http\JsonResponse;
use PHPUnit\Framework\TestCase;
use stdClass;

class JsonResponseTest extends TestCase
{
    public function testWithCode(): void
    {
        $response = new JsonResponse(0, 201);

        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals('0', (string)$response->getBody());
        self::assertEquals(201, $response->getStatusCode());
    }

    public function testWithHeaders(): void
    {
        $response = (new JsonResponse(0))
            ->withHeader('Cache-Control', 'no-cache');

        self::assertEquals('no-cache', $response->getHeaderLine('Cache-Control'));
    }

    /**
     * @dataProvider getCases
     * @param mixed $source
     * @param mixed $expect
     */
    public function testResponse($source, $expect): void
    {
        $response = new JsonResponse($source);

        self::assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        self::assertEquals($expect, (string)$response->getBody());
        self::assertEquals(200, $response->getStatusCode());
    }

    /**
     * @return array<mixed>
     */
    public function getCases(): array
    {
        $object = new stdClass();
        $object->str = 'value';
        $object->int = 12;
        $object->none = null;

        $array = [
            'str' => 'value',
            'int' => 12,
            'none' => null,
        ];

        return [
            'null' => [null, 'null'],
            'empty' => ['', '""'],
            'number' => [12, '12'],
            'string' => ['12', '"12"'],
            'object' => [$object, '{"str":"value","int":12,"none":null}'],
            'array' => [$array, '{"str":"value","int":12,"none":null}'],
        ];
    }
}
