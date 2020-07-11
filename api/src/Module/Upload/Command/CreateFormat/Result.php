<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Command\CreateFormat;

class Result
{
    private string $id;
    private string $extension;
    private string $mimeType;

    public function __construct(string $id, string $extension, string $mimeType)
    {
        $this->id = $id;
        $this->extension = $extension;
        $this->mimeType = $mimeType;
    }

    public function getId(): string
    {
        return $this->id;
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
