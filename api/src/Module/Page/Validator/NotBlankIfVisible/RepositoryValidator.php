<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Validator\NotBlankIfVisible;

use App\Exception\DomainNotFoundException;
use App\Module\Page\Entity\Page\Id;
use App\Module\Page\Entity\Page\PageRepository;
use Symfony\Component\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class RepositoryValidator extends Validator
{
    private PageRepository $pages;

    public function __construct(PageRepository $pages, PropertyAccessorInterface $propertyAccessor)
    {
        parent::__construct($propertyAccessor);
        $this->pages = $pages;
    }

    protected function isVisible(NotBlankIfVisible $constraint): bool
    {
        try {
            /** @var mixed $isVisible */
            return parent::isVisible($constraint);
        } catch (PropertyAccess\Exception\UnexpectedTypeException | PropertyAccess\Exception\AccessException $e) {
        }

        $object = $this->context->getObject();
        if ($object === null) {
            throw new UnexpectedValueException($object, 'null');
        }

        /** @var mixed $id */
        $id = $this->propertyAccessor->getValue($object, $constraint->idPath);
        if (!is_string($id)) {
            throw new UnexpectedValueException($id, 'string');
        }
        /** @var string $id */

        $page = $this->pages->find(new Id($id));
        if (!$page) {
            throw new DomainNotFoundException();
        }

        return $page->isVisible();
    }
}
