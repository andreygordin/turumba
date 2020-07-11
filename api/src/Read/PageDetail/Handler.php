<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Read\PageDetail;

use App\Exception\DomainNotFoundException;
use App\Exception\QueryException;
use App\Read\PageDetail\Result\Page;
use App\Read\PageDetail\Service\PageDenormalizer;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\ResultStatement;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

class Handler
{
    private Connection $connection;
    private PageDenormalizer $pageDenormalizer;
    private ?Query $query = null;

    public function __construct(Connection $connection, PageDenormalizer $pageDenormalizer)
    {
        $this->connection = $connection;
        $this->pageDenormalizer = $pageDenormalizer;
    }

    public function withQuery(Query $query): self
    {
        try {
            Assert::uuid($query->id);
        } catch (InvalidArgumentException $e) {
            throw new QueryException($e->getMessage(), (int)$e->getCode(), $e->getPrevious());
        }

        $handler = clone $this;
        $handler->query = $query;
        return $handler;
    }

    public function handle(): Page
    {
        Assert::notNull($this->query, 'You need to set query first');

        /** @var ResultStatement $stmt */
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                ['id', 'full_name', 'is_visible', 'note', 'parent_id', 'short_name', 'slug1', 'slug2', 'slug3', 'title']
            )
            ->from('page')
            ->andWhere('id = :id')->setParameter(':id', $this->query->id)
            ->execute();

        /** @var array|false $data */
        $data = $stmt->fetch();

        if ($data === false) {
            throw new DomainNotFoundException();
        }

        return $this->pageDenormalizer
            ->withFormatter($this->query->format)
            ->denormalize($data);
    }
}
