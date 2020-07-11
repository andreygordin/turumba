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

class ShortNameType extends StringType
{
    public const NAME = 'page_page_short_name';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $strValue = $value instanceof ShortName ? $value->getValue() : (string)$value;
        return $strValue ?: null;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new ShortName((string)$value);
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
