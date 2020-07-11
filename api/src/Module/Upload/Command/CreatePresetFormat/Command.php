<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Command\CreatePresetFormat;

class Command
{
    public string $id = '';
    public string $preset = '';
    public string $extension = '';
}
