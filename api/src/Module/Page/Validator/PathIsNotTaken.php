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
class PathIsNotTaken extends Constraint
{
    public const PATH_IS_TAKEN_ERROR = '399d7c28-586a-45d4-b9ba-426672df1b36';

    public string $slug1Path = '';
    public string $slug2Path = '';
    public string $slug3Path = '';
    public string $idPath = '';
    public string $message = 'Page with such a path already exists';

    public function getRequiredOptions()
    {
        return ['slug1Path', 'slug2Path', 'slug3Path'];
    }
}
