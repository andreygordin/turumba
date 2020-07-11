<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Entity\Image;

class Variation
{
    use FormatTrait;

    private Preset $preset;

    public function __construct(Preset $preset)
    {
        $this->preset = $preset;
        $this->formats = new FormatCollection();
    }

    public function getPreset(): Preset
    {
        return $this->preset;
    }
}
