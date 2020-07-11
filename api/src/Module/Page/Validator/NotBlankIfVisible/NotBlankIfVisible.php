<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Validator\NotBlankIfVisible;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class NotBlankIfVisible extends Constraint
{
    public string $idPath = '';
    public string $visibilityPath = '';
    public string $message = 'This value should not be blank.';

    public function getRequiredOptions()
    {
        return ['visibilityPath'];
    }

    public function validatedBy()
    {
        return empty($this->idPath)
            ? Validator::class
            : RepositoryValidator::class;
    }
}
