<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

return [
    'config' => [
        'logger' => [
            'file' => __DIR__ . '/../../var/log/' . PHP_SAPI . '/test.log',
            'stderr' => false,
        ],
    ],
];
