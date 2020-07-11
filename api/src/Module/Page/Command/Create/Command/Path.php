<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Command\Create\Command;

use Symfony\Component\Validator\Constraints as Assert;

class Path
{
    /**
     * @Assert\Regex(pattern="/^[-a-z\d]*$/i", normalizer="trim")
     * @Assert\Length(max=255, allowEmptyString=true)
     */
    public string $slug1 = '';

    /**
     * @Assert\Regex(pattern="/^[-a-z\d]*$/i", normalizer="trim")
     * @Assert\Length(max=255, allowEmptyString=true)
     */
    public string $slug2 = '';

    /**
     * @Assert\Regex(pattern="/^[-a-z\d]*$/i", normalizer="trim")
     * @Assert\Length(max=255, allowEmptyString=true)
     */
    public string $slug3 = '';
}
