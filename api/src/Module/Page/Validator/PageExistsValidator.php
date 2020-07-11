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
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class PageExistsValidator extends ConstraintValidator
{
    private PageRepository $pages;

    public function __construct(PageRepository $pages)
    {
        $this->pages = $pages;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof PageExists) {
            throw new UnexpectedTypeException($constraint, PageExists::class);
        }

        if (empty($value)) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!$this->pages->exists(new Id($value))) {
            $this->context
                ->buildViolation($constraint->message)
                ->setCode(PageExists::PAGE_DOESNT_EXIST_ERROR)
                ->addViolation();
        }
    }
}
