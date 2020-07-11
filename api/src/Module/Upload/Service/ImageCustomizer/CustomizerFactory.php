<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Service\ImageCustomizer;

use App\Module\Upload\Service\TinifyKeyRepository;
use Imagine\Image\ImagineInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class CustomizerFactory
{
    private ImagineInterface $imagine;
    private TinifyKeyRepository $tinifyKeyRepository;
    private StreamFactoryInterface $streamFactory;

    public function __construct(
        ImagineInterface $imagine,
        TinifyKeyRepository $tinifyKeyRepository,
        StreamFactoryInterface $streamFactory
    ) {
        $this->imagine = $imagine;
        $this->tinifyKeyRepository = $tinifyKeyRepository;
        $this->streamFactory = $streamFactory;
    }

    public function create(StreamInterface $stream): Customizer
    {
        return new Customizer($stream, $this->imagine, $this->tinifyKeyRepository, $this->streamFactory);
    }
}
