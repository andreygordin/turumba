<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Entity\Image;

use Webmozart\Assert\Assert;

class Format
{
    public const JPEG = 1;
    public const PNG = 2;
    public const WEBP = 3;

    private int $value;

    public function __construct(int $value)
    {
        Assert::inArray($value, [self::JPEG, self::PNG, self::WEBP]);
        $this->value = $value;
    }

    public static function createForExtension(string $ext): self
    {
        $map = self::getExtFormatMap();
        Assert::inArray($ext, array_keys($map));
        return new self($map[$ext]);
    }

    public static function jpeg(): self
    {
        return new self(self::JPEG);
    }

    public static function png(): self
    {
        return new self(self::PNG);
    }

    public static function webp(): self
    {
        return new self(self::WEBP);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function isEqualTo(self $format): bool
    {
        return $this->value === $format->value;
    }

    public function getExtension(): string
    {
        $map = array_flip($this->getExtFormatMap());
        return $map[$this->value];
    }

    public function getMimeType(): string
    {
        $map = [
            self::JPEG => 'image/jpeg',
            self::PNG => 'image/png',
            self::WEBP => 'image/webp',
        ];
        return $map[$this->value];
    }

    /**
     * @return array<string,int>
     */
    private static function getExtFormatMap(): array
    {
        return [
            'jpg' => self::JPEG,
            'png' => self::PNG,
            'webp' => self::WEBP,
        ];
    }
}
