<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

return [
    'config' => [
        'console' => [
            'commands' => [
                Doctrine\ORM\Tools\Console\Command\SchemaTool\DropCommand::class,
                Doctrine\Migrations\Tools\Console\Command\DiffCommand::class,
                Doctrine\Migrations\Tools\Console\Command\GenerateCommand::class,
            ],
        ],
    ],
];
