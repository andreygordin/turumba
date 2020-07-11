<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

$normalizerDefinition = function (): Serializer {
    $normalizers = [
        new ObjectNormalizer(
            null,
            null,
            null,
            new ReflectionExtractor(),
            null,
            null,
            [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true],
        ),
    ];
    return new Serializer($normalizers);
};

return [
    NormalizerInterface::class => $normalizerDefinition,
    DenormalizerInterface::class => $normalizerDefinition,
];
