<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service;

use Webmozart\Assert\Assert;

class PathGenerator
{
    private string $baseDir;
    private string $originalFileName;

    public function __construct(
        string $baseDir,
        PresetCollection $presetCollection,
        string $originalFileName = 'original'
    ) {
        Assert::directory($baseDir);
        $this->baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR);

        Assert::notEmpty($originalFileName);
        Assert::keyNotExists($presetCollection->toArray(), $originalFileName);
        $this->originalFileName = $originalFileName;
    }

    public function getDirPath(string $id): string
    {
        $id = mb_strtolower($id);
        Assert::uuid($id);
        return $this->baseDir . '/' . $id;
    }

    public function getFilePath(string $id, string $ext, ?string $presetName = null): string
    {
        $fileName = $presetName ?: $this->originalFileName;
        Assert::regex($fileName, '/[-_0-9a-z]/i');

        $ext = mb_strtolower($ext);
        Assert::regex($ext, '/[0-9a-z]/');

        return $this->getDirPath($id) . '/' . $fileName . '.' . $ext;
    }
}
