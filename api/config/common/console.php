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
                App\Console\TempCommand::class,
                Doctrine\ORM\Tools\Console\Command\ValidateSchemaCommand::class,
                Doctrine\Migrations\Tools\Console\Command\ExecuteCommand::class,
                Doctrine\Migrations\Tools\Console\Command\MigrateCommand::class,
                Doctrine\Migrations\Tools\Console\Command\LatestCommand::class,
                Doctrine\Migrations\Tools\Console\Command\StatusCommand::class,
                Doctrine\Migrations\Tools\Console\Command\UpToDateCommand::class,
            ],
        ],
    ],
];
