<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Data\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class NullableStringType extends StringType
{
    public const NAME = 'nullable_string';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return (string)$value ?: null;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return (string)$value;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
