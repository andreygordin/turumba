<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Validator\NotBlankIfVisible;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class Validator extends ConstraintValidator
{
    protected PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotBlankIfVisible) {
            throw new UnexpectedTypeException($constraint, NotBlankIfVisible::class);
        }

        if (empty($value) && $this->isVisible($constraint)) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }

    protected function isVisible(NotBlankIfVisible $constraint): bool
    {
        $object = $this->context->getObject();
        if ($object === null) {
            throw new UnexpectedValueException($object, 'null');
        }
        return (bool)$this->propertyAccessor->getValue($object, $constraint->visibilityPath);
    }
}
