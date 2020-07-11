<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Entity\Page;

use Symfony\Component\String\UnicodeString;
use Webmozart\Assert\Assert;

class FullName
{
    private string $value;

    public function __construct(string $value = '')
    {
        $value = (new UnicodeString($value))
            ->collapseWhitespace()
            ->trim()
            ->toString();

        Assert::maxLength($value, 255);

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isEmpty(): bool
    {
        return empty($this->getValue());
    }

    public function isEqualTo(self $fullName): bool
    {
        return $this->value === $fullName->value;
    }
}
