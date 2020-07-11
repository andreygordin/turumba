<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Slim\CallableResolver;
use Slim\Interfaces\CallableResolverInterface;

return [
    CallableResolverInterface::class => DI\autowire(CallableResolver::class)
        ->constructor(
            function (ContainerInterface $container) {
                return $container;
            }
        ),
];
