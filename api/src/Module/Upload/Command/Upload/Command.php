<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Command\Upload;

use App\Module\Upload\Validator\Image;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @Assert\NotNull()
     * @Image()
     */
    public ?UploadedFileInterface $file = null;
}
