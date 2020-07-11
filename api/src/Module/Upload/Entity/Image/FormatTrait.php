<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Entity\Image;

use DomainException;

trait FormatTrait
{
    private FormatCollection $formats;

    /**
     * @return Format[]
     */
    public function getFormats(): array
    {
        /** @var Format[] */
        return $this->formats->toArray();
    }

    public function hasFormats(): bool
    {
        return !$this->formats->isEmpty();
    }

    public function hasFormat(Format $format): bool
    {
        return $this->formats->exists(
            function (Format $existingFormat) use ($format) {
                return $existingFormat->isEqualTo($format);
            }
        );
    }

    public function addFormat(Format $format): void
    {
        if ($this->hasFormat($format)) {
            throw new DomainException('This format is already defined for this image');
        }
        $this->formats->add($format);
    }

    public function removeFormat(Format $format): void
    {
        /** @var Format|false $format */
        $format = $this->formats->find(
            function (Format $existingFormat) use ($format) {
                return $existingFormat->isEqualTo($format);
            }
        );
        if ($format !== false) {
            $this->formats->removeElement($format);
        }
    }
}
