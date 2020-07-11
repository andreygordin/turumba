<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Handlers\Strategies\RequestHandler;

return function (ContainerInterface $container): App {
    $app = AppFactory::createFromContainer($container);
    $app->getRouteCollector()->setDefaultInvocationStrategy(new RequestHandler(true));
    (require __DIR__ . '/../config/middleware.php')($app, $container);
    (require __DIR__ . '/../config/routes.php')($app);
    return $app;
};
