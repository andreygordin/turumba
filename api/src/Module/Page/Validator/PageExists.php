<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class PageExists extends Constraint
{
    public const PAGE_DOESNT_EXIST_ERROR = '7d4b0e15-792e-40d9-8a04-570e2cd07fd8';

    public string $message = 'Page doesn\'t exist';
}
