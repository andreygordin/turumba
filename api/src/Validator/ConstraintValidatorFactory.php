<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ExpressionValidator;
use Symfony\Component\Validator\ConstraintValidatorFactoryInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use UnexpectedValueException;

class ConstraintValidatorFactory implements ConstraintValidatorFactoryInterface
{
    /**
     * @var ConstraintValidatorInterface[]
     */
    protected array $validators = [];

    /**
     * @var callable
     */
    private $getter;

    public function __construct(callable $getter)
    {
        $this->getter = $getter;
    }

    public function getInstance(Constraint $constraint): ConstraintValidatorInterface
    {
        $className = $constraint->validatedBy();

        if (!isset($this->validators[$className])) {
            if ($className === 'validator.expression') {
                $className = ExpressionValidator::class;
            }
            /** @var mixed $validator */
            $validator = call_user_func($this->getter, $className);
            if (!$validator instanceof ConstraintValidatorInterface) {
                throw new UnexpectedValueException(
                    'ConstraintValidatorFactory\'s getter must return an instance of ConstraintValidatorInterface'
                );
            }
            $this->validators[$className] = $validator;
        }

        return $this->validators[$className];
    }
}
