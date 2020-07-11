<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Validator;

use App\Module\Page\Entity\Page\Id;
use App\Module\Page\Entity\Page\PageRepository;
use App\Module\Page\Entity\Page\Path;
use Symfony\Component\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class PathIsNotTakenValidator extends ConstraintValidator
{
    private PageRepository $pages;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PageRepository $pages, PropertyAccessorInterface $propertyAccessor)
    {
        $this->pages = $pages;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof PathIsNotTaken) {
            throw new UnexpectedTypeException($constraint, PathIsNotTaken::class);
        }

        $object = $this->context->getObject();
        if ($object === null) {
            throw new UnexpectedValueException($object, 'null');
        }

        $slugs = [];
        foreach ([$constraint->slug1Path, $constraint->slug2Path, $constraint->slug3Path] as $slugPath) {
            try {
                $slugs[] = trim((string)$this->propertyAccessor->getValue($object, $slugPath));
            } catch (PropertyAccess\Exception\UnexpectedTypeException | PropertyAccess\Exception\AccessException $e) {
                $slugs[] = '';
            }
        }
        list ($slug1, $slug2, $slug3) = $slugs;

        if (empty($constraint->idPath)) {
            $ignoredIds = [];
        } else {
            /** @var mixed $id */
            $id = $this->propertyAccessor->getValue($object, $constraint->idPath);
            if (!is_string($id)) {
                throw new UnexpectedValueException($id, 'string');
            }
            /** @var string $id */
            $ignoredIds = [new Id($id)];
        }

        if (empty($slug1) && empty($slug2) && empty($slug3)) {
            return;
        }

        $path = new Path($slug1, $slug2, $slug3);

        if ($this->pages->existsByPath($path, $ignoredIds)) {
            $this->context
                ->buildViolation($constraint->message)
                ->setCode(PathIsNotTaken::PATH_IS_TAKEN_ERROR)
                ->addViolation();
        }
    }
}
