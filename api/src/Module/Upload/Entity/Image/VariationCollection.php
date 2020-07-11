<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Entity\Image;

use Arrayy\Collection\AbstractCollection;

class VariationCollection extends AbstractCollection
{
    public function getType(): string
    {
        return Variation::class;
    }

    public function findByPreset(Preset $preset): ?Variation
    {
        /** @var Variation|false $result */
        $result = $this->find(
            function (Variation $variation) use ($preset) {
                return $variation->getPreset() === $preset;
            }
        );
        return $result ?: null;
    }
}
