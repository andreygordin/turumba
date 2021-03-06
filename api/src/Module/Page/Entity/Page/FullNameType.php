<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Entity\Page;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class FullNameType extends StringType
{
    public const NAME = 'page_page_full_name';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $strValue = $value instanceof FullName ? $value->getValue() : (string)$value;
        return $strValue ?: null;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new FullName((string)$value);
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
