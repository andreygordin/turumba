<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Data\Doctrine;

use DateTimeImmutable;
use Doctrine\DBAL\Logging\SQLLogger;
use Monolog\Logger;
use Webmozart\Assert\Assert;

class MonologSQLLogger implements SQLLogger
{
    private Logger $logger;
    private ?DateTimeImmutable $startTime = null;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->logger->info($sql);

        if ($params) {
            $this->logger->info(json_encode($params));
        }

        if ($types) {
            $this->logger->info(json_encode($types));
        }

        $this->startTime = new DateTimeImmutable();
    }

    public function stopQuery()
    {
        Assert::notNull($this->startTime);
        $endTime = new DateTimeImmutable();
        $interval = $endTime->diff($this->startTime);
        $ms = round((float)$interval->format('%f') / 1000, 1);
        $this->logger->info('Query took ' . $ms . 'ms.');
    }
}
