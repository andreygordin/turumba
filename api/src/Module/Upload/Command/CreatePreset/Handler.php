<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Command\CreatePreset;

use App\Exception\DomainNotFoundException;
use App\Module\Upload\Entity\Image\Id;
use App\Module\Upload\Entity\Image\ImageRepository;
use App\Module\Upload\Entity\Image\Preset;
use App\Module\Upload\Entity\Image\Variation;
use App\Module\Upload\Service\PresetCollection;

class Handler
{
    private ImageRepository $images;
    private PresetCollection $presetCollection;

    public function __construct(ImageRepository $images, PresetCollection $presetCollection)
    {
        $this->images = $images;
        $this->presetCollection = $presetCollection;
    }

    public function handle(Command $command): Result
    {
        $id = new Id($command->id);

        $image = $this->images->find($id);
        if (!$image) {
            throw new DomainNotFoundException();
        }

        $presetName = $command->preset;
        /** @var Preset|null $preset */
        $preset = $this->presetCollection->get($presetName);
        if (!$preset) {
            throw new DomainNotFoundException();
        }

        if (!$image->hasVariation($preset)) {
            $image->createVariation($preset);
        }
        /** @var Variation $variation */
        $variation = $image->getVariation($preset);
        $format = $image->getOriginalFormat();
        $variation->addFormat($format);

        $this->images->flush();

        return new Result(
            $image->getId()->getValue(),
            $presetName,
            $format->getExtension(),
            $format->getMimeType()
        );
    }
}
