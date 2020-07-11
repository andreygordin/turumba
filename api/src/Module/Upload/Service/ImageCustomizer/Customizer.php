<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service\ImageCustomizer;

use App\Module\Upload\Service\TinifyKeyRepository;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use InvalidArgumentException;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Tinify;

class Customizer
{
    private ImagineInterface $imagine;
    private TinifyKeyRepository $tinifyKeyRepository;
    private StreamFactoryInterface $streamFactory;
    private ImageInterface $image;

    public function __construct(
        StreamInterface $stream,
        ImagineInterface $imagine,
        TinifyKeyRepository $tinifyKeyRepository,
        StreamFactoryInterface $streamFactory
    ) {
        $this->image = $imagine->load($stream->getContents());
        $this->imagine = $imagine;
        $this->tinifyKeyRepository = $tinifyKeyRepository;
        $this->streamFactory = $streamFactory;
    }

    public function scale(int $width, int $height): self
    {
        $this->image = $this->image->thumbnail(
            new Box($width, $height),
            ImageInterface::THUMBNAIL_INSET,
            ImageInterface::FILTER_SINC
        );
        return $this;
    }

    public function scaleAndCrop(int $width, int $height): self
    {
        $this->image = $this->image->thumbnail(
            new Box($width, $height),
            ImageInterface::THUMBNAIL_OUTBOUND,
            ImageInterface::FILTER_SINC
        );
        return $this;
    }

    public function getStream(string $expectedMimeType): StreamInterface
    {
        switch ($expectedMimeType) {
            case 'image/webp':
                $body = $this->image->get('webp', ['webp_quality' => 65]);
                break;
            case 'image/jpeg':
                $body = $this->image->get('jpg', ['jpeg_quality' => 100]);
                break;
            case 'image/png':
                $body = $this->image->get('png', ['png_compression_level' => 9]);
                break;
            default:
                throw new InvalidArgumentException('Invalid mime type');
        }

        if ($expectedMimeType !== 'image/webp') {
            $key = $this->tinifyKeyRepository->fetch();
            Tinify\setKey($key);
            /** @var Tinify\Source $source */
            $source = Tinify\fromBuffer($body);
            /** @var string $body */
            $body = $source->toBuffer();
        }

        return $this->streamFactory->createStream($body);
    }
}
