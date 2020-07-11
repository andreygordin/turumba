<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Command\CreatePreset;

class Result
{
    private string $id;
    private string $preset;
    private string $extension;
    private string $mimeType;

    public function __construct(string $id, string $preset, string $extension, string $mimeType)
    {
        $this->id = $id;
        $this->preset = $preset;
        $this->extension = $extension;
        $this->mimeType = $mimeType;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPreset(): string
    {
        return $this->preset;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }
}
