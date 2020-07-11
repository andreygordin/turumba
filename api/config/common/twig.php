<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

return [
    Environment::class => function (ContainerInterface $container): Environment {
        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{
         *     debug:bool,
         *     cacheDir:string,
         * } $config
         */
        $config = $container->get('config')['twig'];

        $loader = new ArrayLoader();

        return new Environment(
            $loader,
            [
                'cache' => $config['debug'] ? false : $config['cacheDir'],
                'debug' => $config['debug'],
                'strict_variables' => $config['debug'],
                'auto_reload' => $config['debug'],
            ]
        );
    },

    'config' => [
        'twig' => [
            'debug' => (bool)getenv('APP_DEBUG'),
            'cacheDir' => __DIR__ . '/../../var/cache/' . PHP_SAPI . '/twig',
        ],
    ],
];
