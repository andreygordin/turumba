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
class PathIsValid extends Constraint
{
    public string $slug1Path = '';
    public string $slug2Path = '';
    public string $slug3Path = '';
    public string $message = 'A non-empty slug cannot come after an empty slug.';

    public function getRequiredOptions()
    {
        return ['slug1Path', 'slug2Path', 'slug3Path'];
    }
}
