<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service\StreamHandler;

use App\Module\Upload\Entity\Image\Preset;
use App\Module\Upload\Service\ImageCustomizer\Customizer;
use App\Module\Upload\Service\ImageCustomizer\CustomizerFactory;
use Imagine\Exception\RuntimeException;
use Imagine\Image\ImagineInterface;
use Psr\Http\Message\StreamInterface;

class ImageStreamHandler implements StreamHandlerInterface
{
    private ImagineInterface $imagine;
    private CustomizerFactory $customizerFactory;
    private ?Preset $preset = null;

    public function __construct(ImagineInterface $imagine, CustomizerFactory $customizerFactory)
    {
        $this->imagine = $imagine;
        $this->customizerFactory = $customizerFactory;
    }

    public function setPreset(?Preset $preset): void
    {
        $this->preset = $preset;
    }

    public function withPreset(?Preset $preset): self
    {
        $handler = clone $this;
        $handler->preset = $preset;
        return $handler;
    }

    public function handle(StreamInterface &$stream, string $expectedMimeType): void
    {
        try {
            $this->imagine->load($stream->getContents());
        } catch (RuntimeException $e) {
            throw new ImageException('Image is not valid');
        }
        $stream->rewind();

        $customizer = $this->customizerFactory->create($stream);

        if ($this->preset !== null) {
            $presetHandler = $this->preset->getHandler();
            /** @var Customizer $customizer */
            $customizer = call_user_func($presetHandler, $customizer);
        }

        $stream = $customizer->getStream($expectedMimeType);
    }
}
