<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Http\Validator;

use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Webmozart\Assert\Assert;

class ConflictViolationDetector
{
    /**
     * @var string[]
     */
    private array $conflictViolationCodes;

    public function __construct(array $conflictViolationCodes = [])
    {
        Assert::allStringNotEmpty($conflictViolationCodes);
        /** @var string[] $conflictViolationCodes */
        $this->conflictViolationCodes = $conflictViolationCodes;
    }

    public function isConflict(ConstraintViolationInterface $violation): bool
    {
        foreach ($this->conflictViolationCodes as $code) {
            if ($violation->getCode() === $code) {
                return true;
            }
        }
        return false;
    }

    public function hasConflicts(ConstraintViolationListInterface $violations): bool
    {
        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            if ($this->isConflict($violation)) {
                return true;
            }
        }
        return false;
    }
}
