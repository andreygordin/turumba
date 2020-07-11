<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

use App\Http\Middleware\ClearEmptyParams;
use App\Http\Middleware\DomainExceptionHandler;
use App\Http\Middleware\QueryExceptionHandler;
use App\Http\Middleware\ValidationExceptionHandler;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;

return function (App $app): void {
    $app->add(DomainExceptionHandler::class);
    $app->add(ValidationExceptionHandler::class);
    $app->add(QueryExceptionHandler::class);
    $app->add(ClearEmptyParams::class);
    $app->addBodyParsingMiddleware();
    $app->add(ErrorMiddleware::class);
};
