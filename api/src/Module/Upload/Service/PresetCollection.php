<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service;

use App\Module\Upload\Entity\Image\Preset;
use Arrayy\ArrayyIterator;
use Arrayy\Collection\AbstractCollection;
use Webmozart\Assert\Assert;

class PresetCollection extends AbstractCollection
{
    /**
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function __construct(
        array $data = [],
        string $iteratorClass = ArrayyIterator::class,
        bool $checkPropertiesInConstructor = true
    ) {
        parent::__construct($data, $iteratorClass, $checkPropertiesInConstructor);
        Assert::allString(array_keys($data));
        Assert::allRegex(array_keys($data), '/[-_0-9a-z]/i');
    }

    public function getType(): string
    {
        return Preset::class;
    }
}
