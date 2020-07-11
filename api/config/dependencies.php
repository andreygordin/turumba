<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

use Laminas\ConfigAggregator\ConfigAggregator;
use Laminas\ConfigAggregator\PhpFileProvider;

$aggregator = new ConfigAggregator(
    [
        new PhpFileProvider(__DIR__ . '/common/*.php'),
        new PhpFileProvider(__DIR__ . '/' . (getenv('APP_ENV') ?: 'prod') . '/*.php'),
    ]
);

return $aggregator->getMergedConfig();
