<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Validator;

use Symfony\Component\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class PathIsValidValidator extends ConstraintValidator
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof PathIsValid) {
            throw new UnexpectedTypeException($constraint, PathIsValid::class);
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

        $emptyFound = false;
        foreach ($slugs as $slug) {
            if ($emptyFound && !empty($slug)) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();
            }
            $emptyFound = $emptyFound || empty($slug);
        }
    }
}
