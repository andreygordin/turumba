<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service;

use Webmozart\Assert\Assert;

class AccelPathGenerator
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        $basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
        Assert::notEmpty($basePath);
        $this->basePath = $basePath;
    }

    public function getFilePath(string $id, string $ext, ?string $presetName = null): string
    {
        $id = mb_strtolower($id);
        Assert::uuid($id);

        $ext = mb_strtolower($ext);
        Assert::regex($ext, '/[0-9a-z]/');

        Assert::nullOrRegex($presetName, '/[-_0-9a-z]/i');

        return $this->basePath
            . '/' . $id
            . ($presetName ? '/' . $presetName : '')
            . '/file.' . $ext;
    }
}
