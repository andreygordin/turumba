<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Command\Upload;

class Result
{
    private string $id;
    private string $extension;

    public function __construct(string $id, string $extension)
    {
        $this->id = $id;
        $this->extension = $extension;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }
}
