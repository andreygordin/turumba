<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service;

use InvalidArgumentException;
use Webmozart\Assert\Assert;

class UrlGenerator
{
    private string $baseUrl;

    public function __construct(string $baseUrl)
    {
        if (!filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('Base url is invalid');
        }
        $this->baseUrl = rtrim($baseUrl, DIRECTORY_SEPARATOR);
    }

    public function getAbsoluteUrl(string $id, string $fileName, string $ext, ?string $presetName = null): string
    {
        return $this->baseUrl . $this->getRelativeUrl($id, $fileName, $ext, $presetName);
    }

    public function getRelativeUrl(string $id, string $fileName, string $ext, ?string $presetName = null): string
    {
        $id = mb_strtolower($id);
        Assert::uuid($id);

        Assert::regex($fileName, '/[-_0-9a-z]/i');

        $ext = mb_strtolower($ext);
        Assert::regex($ext, '/[0-9a-z]/');

        Assert::nullOrRegex($presetName, '/[-_0-9a-z]/i');

        return '/' . $id
            . ($presetName ? '/' . $presetName : '')
            . '/' . $fileName . '.' . $ext;
    }
}
