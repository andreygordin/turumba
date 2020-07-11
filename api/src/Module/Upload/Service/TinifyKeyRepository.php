<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service;

use Webmozart\Assert\Assert;

class TinifyKeyRepository
{
    /**
     * @var string[]
     */
    private array $keys;

    public function __construct(array $keys)
    {
        Assert::notEmpty($keys);
        Assert::allStringNotEmpty($keys);
        /** @var string[] keys */
        $this->keys = $keys;
    }

    public function fetch(): string
    {
        return $this->keys[array_rand($this->keys)];
    }
}
