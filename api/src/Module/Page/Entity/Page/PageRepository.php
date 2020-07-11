<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Entity\Page;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;

class PageRepository
{
    private EntityManagerInterface $em;
    private EntityRepository $repo;

    public function __construct(EntityManagerInterface $em, EntityRepository $repo)
    {
        $this->em = $em;
        $this->repo = $repo;
    }

    public function get(Id $id): Page
    {
        if (!$page = $this->find($id)) {
            throw new DomainException('Page not found.');
        }
        /** @var Page $page */
        return $page;
    }

    public function find(Id $id): ?Page
    {
        /** @var Page $page */
        $page = $this->repo->find($id->getValue());
        return $page;
    }

    public function exists(Id $id): bool
    {
        return $this->repo->count(['id' => $id->getValue()]) > 0;
    }

    public function add(Page $page): void
    {
        $this->em->persist($page);
    }

    public function remove(Page $page): void
    {
        if ($page->hasChildren()) {
            throw new DomainException('Cannot delete page with children.');
        }
        $this->em->remove($page);
    }

    /**
     * @param Path $path
     * @param Id[] $ignoredIds
     * @return bool
     */
    public function existsByPath(Path $path, array $ignoredIds = []): bool
    {
        $queryBuilder = $this->repo->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.path.slug1 = :slug1')->setParameter(':slug1', $path->getSlug1())
            ->andWhere('p.path.slug2 = :slug2')->setParameter(':slug2', $path->getSlug2())
            ->andWhere('p.path.slug3 = :slug3')->setParameter(':slug3', $path->getSlug3());

        if (!empty($ignoredIds)) {
            $ignoredIdValues = array_map(fn($ignoreId): string => $ignoreId->getValue(), $ignoredIds);
            $queryBuilder
                ->andWhere('p.id NOT IN (:ignoredIds)')->setParameter(':ignoredIds', $ignoredIdValues);
        }

        return $queryBuilder->getQuery()->getSingleScalarResult() > 0;
    }
}
