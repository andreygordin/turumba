<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Entity\Page;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TextType;

class NoteType extends TextType
{
    public const NAME = 'page_page_note';

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $strValue = $value instanceof Note ? $value->getValue() : (string)$value;
        return $strValue ?: null;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return new Note((string)$value);
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
