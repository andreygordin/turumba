<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Read\Service;

trait FormatterTrait
{
    private bool $useFormatter = false;

    public function withFormatter(bool $value = true): self
    {
        $denormalizer = clone $this;
        $denormalizer->useFormatter = $value;
        return $denormalizer;
    }
}
