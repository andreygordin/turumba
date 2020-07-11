<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

use App\Module\Page\Entity\Page\Page;
use App\Module\Page\Entity\Page\PageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

return [
    PageRepository::class => DI\autowire(PageRepository::class)
        ->constructorParameter(
            'repo',
            function (ContainerInterface $container): PageRepository {
                /** @var EntityManagerInterface $em */
                $em = $container->get(EntityManagerInterface::class);
                /** @var PageRepository $repository */
                $repository = $em->getRepository(Page::class);
                return $repository;
            }
        ),
];
