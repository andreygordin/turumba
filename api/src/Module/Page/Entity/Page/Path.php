<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Entity\Page;

use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\String\UnicodeString;
use Webmozart\Assert\Assert;

/**
 * @ORM\Embeddable
 */
class Path
{
    /**
     * @ORM\Column(type="nullable_string", nullable=true)
     */
    private string $slug1;

    /**
     * @ORM\Column(type="nullable_string", nullable=true)
     */
    private string $slug2;

    /**
     * @ORM\Column(type="nullable_string", nullable=true)
     */
    private string $slug3;

    public function __construct(string $slug1, string $slug2, string $slug3)
    {
        foreach ([&$slug1, &$slug2, &$slug3] as &$slug) {
            $slug = (new UnicodeString($slug))
                ->lower()
                ->trim()
                ->toString();
        }

        /** @psalm-suppress MixedArgument */
        self::validate($slug1, $slug2, $slug3);

        $this->slug1 = $slug1;
        $this->slug2 = $slug2;
        $this->slug3 = $slug3;
    }

    private static function validate(string $slug1, string $slug2, string $slug3): void
    {
        $slugs = [$slug1, $slug2, $slug3];

        Assert::allRegex($slugs, '/^[-a-z\d]*$/i');
        Assert::allMaxLength($slugs, 255);

        $emptyFound = false;
        foreach ($slugs as $slug) {
            if ($emptyFound && !empty($slug)) {
                throw new InvalidArgumentException('A non-empty slug cannot come after an empty slug.');
            }
            $emptyFound = $emptyFound || empty($slug);
        }
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function getSlug1(): string
    {
        return $this->slug1;
    }

    public function getSlug2(): string
    {
        return $this->slug2;
    }

    public function getSlug3(): string
    {
        return $this->slug3;
    }

    public function getValue(): string
    {
        return $this->getSlug1() . '/' . $this->getSlug2() . '/' . $this->getSlug3();
    }

    public function isEqualTo(self $path): bool
    {
        return $this->getValue() === $path->getValue();
    }
}
