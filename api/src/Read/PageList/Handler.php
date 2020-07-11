<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Read\PageList;

use App\Exception\QueryException;
use App\Read\PageList\Result\Page;
use App\Read\PageList\Result\PageCollection;
use App\Read\PageList\Service\PageDenormalizer;
use Arrayy\Arrayy as A;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\Query\QueryBuilder;
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
            Assert::nullOrUuid($query->parentId);
            Assert::nullOrGreaterThanEq($query->depth, 0);
        } catch (InvalidArgumentException $e) {
            throw new QueryException($e->getMessage(), (int)$e->getCode(), $e->getPrevious());
        }

        $handler = clone $this;
        $handler->query = $query;
        return $handler;
    }

    public function handle(): PageCollection
    {
        Assert::notNull($this->query, 'You need to set query first');

        $queryBuilder = $this->getBaseQueryBuilder();

        if ($this->query->parentId === null) {
            $queryBuilder->andWhere('parent_id IS NULL');
        } else {
            $queryBuilder
                ->andWhere('parent_id = :parentId')
                ->setParameter(':parentId', $this->query->parentId);
        }

        /** @var ResultStatement $stmt */
        $stmt = $queryBuilder->execute();

        $pages = new PageCollection();

        /** @psalm-suppress MixedAssignment */
        while ($data = $stmt->fetch()) {
            /** @var array $data */
            $page = $this->pageDenormalizer
                ->withFormatter($this->query->format)
                ->denormalize($data);
            $pages->append($page);
        }

        $this->fetchChildren($pages, $this->query->depth);

        return $pages;
    }

    private function fetchChildren(PageCollection $pages, ?int $depth = null): void
    {
        if ($pages->count() === 0 || ($depth !== null && $depth <= 0)) {
            return;
        }

        Assert::notNull($this->query, 'You need to set query first');

        $pageIds = A::create($pages)->getColumn('id');

        $queryBuilder = $this->getBaseQueryBuilder()
            ->andWhere('parent_id IN (:parentIds)')
            ->setParameter(':parentIds', $pageIds, Connection::PARAM_STR_ARRAY);

        /** @var ResultStatement $stmt */
        $stmt = $queryBuilder->execute();

        $fetchedPages = new PageCollection();

        /** @psalm-suppress MixedAssignment */
        while ($data = $stmt->fetch()) {
            /** @var array $data */
            $fetchedPage = $this->pageDenormalizer
                ->withFormatter($this->query->format)
                ->denormalize($data);
            $fetchedPages->append($fetchedPage);
            /** @var Page $parent */
            $parent = $pages->find(fn(Page $page): bool => $page->id === $fetchedPage->parentId);
            $parent->children->append($fetchedPage);
        }

        if ($depth !== null) {
            $depth--;
        }

        $this->fetchChildren($fetchedPages, $depth);
    }

    private function getBaseQueryBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()
            ->select(
                ['id', 'full_name', 'is_visible', 'note', 'parent_id', 'short_name', 'slug1', 'slug2', 'slug3', 'title']
            )
            ->from('page')
            ->addOrderBy('COALESCE(short_name, full_name)', 'ASC NULLS FIRST');
    }
}
