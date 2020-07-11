<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

use App\Module\Upload\Entity\Image\Preset;
use App\Module\Upload\Service\AccelPathGenerator;
use App\Module\Upload\Service\ImageCustomizer\Customizer;
use App\Module\Upload\Service\PathGenerator;
use App\Module\Upload\Service\PresetCollection;
use App\Module\Upload\Service\Saver\SaverFactory;
use App\Module\Upload\Service\StreamHandler\ImageStreamHandler;
use App\Module\Upload\Service\StreamHandler\StreamHandlerInterface;
use App\Module\Upload\Service\TinifyKeyRepository;
use App\Module\Upload\Service\UrlGenerator;
use Imagine\Image\ImagineInterface;
use Imagine\Imagick\Imagine;
use Psr\Container\ContainerInterface;

return [
    ImagineInterface::class => DI\autowire(Imagine::class),

    SaverFactory::class => DI\autowire()
        ->constructorParameter('streamHandler', DI\get(StreamHandlerInterface::class)),

    StreamHandlerInterface::class => DI\autowire(ImageStreamHandler::class)
        ->method(
            'setPreset',
            function (ContainerInterface $container): Preset {
                /**
                 * @psalm-suppress MixedArrayAccess
                 * @psalm-var array{
                 *     baseDir:string,
                 *     accelDir:string,
                 *     baseUrl:string,
                 *     presets:array<string,Preset>,
                 *     tinifyApiKeys:string[],
                 * } $config
                 */
                $config = $container->get('config')['upload'];
                return $config['presets']['w1920'];
            }
        ),

    PresetCollection::class => DI\autowire()
        ->constructorParameter(
            'data',
            function (ContainerInterface $container): array {
                /**
                 * @psalm-suppress MixedArrayAccess
                 * @psalm-var array{
                 *     baseDir:string,
                 *     accelDir:string,
                 *     baseUrl:string,
                 *     presets:array<string,Preset>,
                 *     tinifyApiKeys:string[],
                 * } $config
                 */
                $config = $container->get('config')['upload'];
                return $config['presets'];
            }
        ),

    PathGenerator::class => DI\autowire()
        ->constructor(
            function (ContainerInterface $container): string {
                /**
                 * @psalm-suppress MixedArrayAccess
                 * @psalm-var array{
                 *     baseDir:string,
                 *     accelDir:string,
                 *     baseUrl:string,
                 *     presets:array<string,Preset>,
                 *     tinifyApiKeys:string[],
                 * } $config
                 */
                $config = $container->get('config')['upload'];
                return $config['baseDir'];
            }
        ),

    UrlGenerator::class => DI\autowire()
        ->constructor(
            function (ContainerInterface $container): string {
                /**
                 * @psalm-suppress MixedArrayAccess
                 * @psalm-var array{
                 *     baseDir:string,
                 *     accelDir:string,
                 *     baseUrl:string,
                 *     presets:array<string,Preset>,
                 *     tinifyApiKeys:string[],
                 * } $config
                 */
                $config = $container->get('config')['upload'];
                return $config['baseUrl'];
            }
        ),

    AccelPathGenerator::class => DI\autowire()
        ->constructor(
            function (ContainerInterface $container): string {
                /**
                 * @psalm-suppress MixedArrayAccess
                 * @psalm-var array{
                 *     baseDir:string,
                 *     accelDir:string,
                 *     baseUrl:string,
                 *     presets:array<string,Preset>,
                 *     tinifyApiKeys:string[],
                 * } $config
                 */
                $config = $container->get('config')['upload'];
                return $config['accelDir'];
            }
        ),

    TinifyKeyRepository::class => DI\autowire()
        ->constructor(
            function (ContainerInterface $container): array {
                /**
                 * @psalm-suppress MixedArrayAccess
                 * @psalm-var array{
                 *     baseDir:string,
                 *     accelDir:string,
                 *     baseUrl:string,
                 *     presets:array<string,Preset>,
                 *     tinifyApiKeys:string[],
                 * } $config
                 */
                $config = $container->get('config')['upload'];
                return $config['tinifyApiKeys'];
            }
        ),

    'config' => [
        'upload' => [
            'baseDir' => __DIR__ . '/../../var/upload',
            'accelDir' => '/upload-proxy',
            'baseUrl' => getenv('UPLOAD_URL'),
            'presets' => [
                'w1920' => new Preset(
                    function (Customizer $customizer): Customizer {
                        return $customizer->scale(1920, 1920);
                    }
                ),
                't50x50' => new Preset(
                    function (Customizer $customizer): Customizer {
                        return $customizer->scaleAndCrop(50, 50);
                    }
                ),
                't32x32' => new Preset(
                    function (Customizer $customizer): Customizer {
                        return $customizer->scaleAndCrop(32, 32);
                    }
                ),
            ],
            'tinifyApiKeys' => [
                'RSzuNkXGSVobTuWcEzKxvalpuJLXG13y',
                'rUgfJ7LFk9x70xi6iZrjZInujKdnH_TA',
                'cnyOi0AigrmeI0HVXjiXIdWlPOz1KLTY',
                '1Pwbj4vFtDHDjALPntiXd6MfeWsJOr9Q',
                'pxi3aDRoK-WPCbYoi3rexsLKDRExFLyy',
                'dg11_5pKdGB1zH9Khf_ZFm-pufB4VvLP',
                'i2STLA8lHrj824RUNdZK2yTnfgI4LBlv',
                'v7u6O9bAR8IAaApNLpYvk-l2FhXpeRO0',
                'gK5De6ul_eSr_MTX1P9Im1AvFeeJskd7',
                'NOdiBPYfOYxFrwvtUWod6jaLAMY_Xitd',
                'Bu_GUucxkeGAXInDkzdRoHFs7h1ihoKj',
                'RLWnDLQmeuCLrnQSUCwFAfU4PxRLA6ah',
                'S4r3E4ulbtZTuF8OHwkIOHrbbpDc0rPV',
                'SRnFewAICbTmEEMU6nzb7FDQzOHS5cX_',
                'Za1iHUPcfW4fOT_4V255cT9S1sRra-ZR',
                'jtI6VBCp9BMEmVdyHzao-Yh-7pkbbltF',
                '7viJSA5c1PC0bbimmjWXaNW4v5Sbaiit',
                'aRZpIoCnCL5TEyC6AP4M4cWMn8Uo0Gk0',
                'fVrm3A5-RzSHamAVB_b_JBR3lkFIrK7W',
                '3hm4hsjvC69WUy1kUbo0zB_ySzunJNNO',
                'VnOvM1EE2-aUA9CJOe-JivspY0AYfwNG',
                'IyDavJzO01qOeZfq-kmBEPYiesrBbXcH',
                'QBmFXUWh65fwEofn6Hz8iYGV81JKRX8C',
                '8QTkM9rWoHrYZjEf49btZ2SmiXHgUbKE',
                'YQfhT2Bx8GSbPwEU9dmKdO3cIwkJQyAB',
                'reytKk5Xx-JJMrsFA9qKSnho4ETAagw3',
                'S5vmo8vgvz3mCMOgwjQtkS-jWXXT-W4v',
                'PPVuuWQbv_CPM7um1Nz0f1t4iCw2p9ks',
                'WfteZ8h7_LNVs9VEFdEuqQPVIh1Rmdfc',
                'GFN63SlQXGZ0ynybavmv7xnoiQn4fuw0',
                'UKCqs1dobSZUSXyRMCVfiRnhuOhvrnfA',
                'rsMkAd-0vkjm-oVYa3tkEMIDlkj2suIY',
                'sNYih8v8o2nDqiM9lvOp-nU6FaxURIJo',
                '-bY2BXGtN4wQZAUSgrdtWMJ3WIEWM6Je',
                'ATlKEgIqjNTfcY-1Z-gYL7Ux9TiMyNpz',
                'Z3NJHxd6KW6Fzfow9WY9h--gkdN9ho9P',
                '0QN49n4bMyzlY0QYS4sDnx1KIGsqvnE9',
                'NSjg3ahiNAJ7qjXlKfRIHWl3PqR1GAb0',
                'OLqUfB0mdk_-y9_aQBKD7Sl5_LsqEMdJ',
                'LAryrG-r5uWJrkKiVFw06bDWt2LZzCw_',
                '5miDulPsPf6lrfBwYUx0MzELsdOhOLy6',
                'aqdWZHyQILvqiGyKMk61xj1eB2Bk8kka',
                'B2M2pAKbwGOef3CvBBC-iZZ0yYI4bEEn',
                'PHWArm7G4_NYIBaWifdhH-Rlvn1Qom4A',
                'HEmXAhvE3t4k-HM8bvE7OsTwdBbgS5-M',
                '47lJA_u2wKRtjcfUbL8liKTH9O931LVm',
                'cRYOImVKHYQ4TK9Ywpx9pIjZdXx3q_NL',
                'TfWm6oYThSVNueHcAvMgfLJFQpKb2yyI',
                'UbA_iALgWCoubqwK6WS0fpPutNB7YOti',
                '4iICKJmL0K8R1tS91giHQ4dIG5Sa5IOX',
                'sORmk6CIdUu4vbfA1JrxtJqFfs2jum8N',
                '1Gdi683cbaRbZjCxXb4PenzEnkFcwlA4',
                'ADTc_S0BNE5QYw2ckEki5LZh5HFwVyJW',
                'f7qHZ24TN9HxVtPrvZfiozxtz_OkkqyC',
                'asvmmmW_K8QRRyJEbfJcaA-bkuBv_Vkn',
                'caTxZxF8XDr_IhQaFeCHjtAYZy2CsNOo',
                'nr74eQRRdWrYefz4J2iXxU1RMNtt5r42',
                'aN9hxariw2nYyqCMgJgjbKsr6MVT8O5l',
                'oBulKtwVn2kgdsyY2zwNFjZuwvDuZ9kH',
                'wjfgvGJkgU7iOtkCu5vs2qAqo5xtfKwL',
                '3BODoRvtcfEUl6W0GduTDW3pln6JxBj5',
                'eRNpMivmV0m15B1xt5fhO7rvMnQ01l2l',
                'PUmglqEJqmyxJMA76r7WjA5bvRk0vzGr',
                'qZ17S7083V9nccGpZQOzQyjQbasNQY11',
                'We2VCssc0HVaNQUAw57R6vfdxHLDDcm9',
                'aPxp8yNYTgr1XapK7WwMt41HBF0a6MDk',
                'fauba1xUVvZAJHe2QaEJFjJnrsEHFVD1',
                'nHboUD0PmQIugN9l1zScWdl2iHI4nQmW',
                'w3NV2mguvj2ZWwlcVW7NRbBPasSk03UH',
                'DRoVxVOitIFnpr72sFGw8cJ1xdkboG3S',
                'cXTUnUtExpMkykaAm9WtVLCrYE1V1Axo',
                'l6EgnHEVzL8Tc6IJsEq8XhLI1bDfT4LW',
                '8J8ZGQ9nOHSqG3B6HD9JNEVPV4YvYFz5',
                '2G0Eacu86qUKHwzTagMjAVMLh23b9Dal',
                'Xw3VSRGjbCfzrD7Z7MXOyfT1gNXZxFJW',
                'QU7KRvziIC9vqCoBIeZ1GySVC3j9v46f',
                'ouL7a9e6fIF8rOgpSGqSGXA36Hlm9map',
                'b99Aeut6bfZjbBuUTMsOHEeyagO1x85b',
                'dVghunjc7ehItML9Qb25BhjMX2EIXJko',
                'plHEcpsJq0eZfMtMVAsbmCfu80VTVdGm',
                'wgS7ABDCFoPQH8Yd4k581j69J5TlDGs2',
                'FqgENG1DWAmSx826EMMhd0MHp5xDxwaH',
                '0DJPKIxydyVUrxLkfbDixYgdlCg5bpOf',
                'qeDaC5W4Dq0EUg7btfM1wKZAhOfYnKld',
                '68DD7wEyJvmJRUblHKwePL7Cp2dmosCN',
                '3rItAYM15pwBwc93upGvSK7CdcPSCICQ',
                'zDUjUGhqjlwGuOEupiTHmK3AAQ4OnqCP',
                'Hfq1FmU5POIQLcGEWv2gaOxduJMdPvNc',
                'qpVpnwbsmJLmQgjiyKsVAlg7hM3SMdM6',
                'xOEiUOcv8yVUrjb0kIEAbS4csVVnUen1',
                'B2CsCvxONP4KyI_FZgT_pvPIB-_YYhOr',
                'IVSfmVTKDKnG6fDn2LPqyRwtlwzhSxlj',
                '6dsx6kxpTWnvmtp3qAsFMk7JyJeUyMqE',
                'EvQyKYpNhyU06AAaLbYhORrsDYlwQja1',
                'mQe5rwQVZJCac9wIX0fbrtuo6OS882fs',
                '3KLJxp79rzA4RgEETyRSDWOs2XwazwA9',
                'Gn5ihVia5nMsgrotKzRLI0CjJORBcpzB',
                'orPuFLC4b5SMPNflNGBzXBkqtmtkB6gs',
                'T2GuvEaN4GPKEAV7tV5H3EJZvPNIoaSI',
                'eR5MMvWnu7lgD5HoTg7HBbeXnygL8yzh',
                'GOjJMn02GMc9LNuujqzMKAd6aDFjWT6m',
                'E4krgQmKlMMqy4GgWqth50E4e3ccENzK',
                'MNKUoORvuXLvlm9NoYUjYFqRq00ZVUgD',
                'q9q39UKfaJCHjrspG04SggAfZYYdJSOf',
                'tcerD1a0j9ojkIXHcHGABfzo1fAgRArG',
                'GwQGTjCKJ9Xpkp9cEZKOS9NU8O3rZyeh',
                'UdxLothEjBJo4lod2IOMOw2Ofu3WzCZH',
                '19UxEbTVjuCwJw1S1WFhinSy9YbW01d5',
                'Dc9abtpTgjv7tME8x16SbwPS0fBmwfRS',
                'Dy4K8CWCyO5sKqReBHlujV8cq9spBsbA',
                'K2D5ROSgyIdm7e4gyQQIZuldFOrCMNHd',
                '9os3f90Vu3F5Cse1seyqHpZWjQRinEos',
                'cCeg8t15zDnGsYFPFlIyzK1BVHF4Up3x',
                'EJO3bZXekfL88IthiYaUEqfnxYt06oSM',
                'nkiQkI2TUv63Zz6GsC6Sy3oiN5ljUOlt',
                'zukaLuzXaKsY1yVqwHOZfXBLGpyaGIwN',
                'K9BNLb0u6ZGfzLpvQB40QbMR5t0IJpZx',
                'qTjzQI9iszLUBX5Zqy02cuVGUyAOFWQb',
                '9BdsI2WEVC9mZ6HjaDg21p40w1rgIJKj',
                '9jJxtIORQk9ddcySNoIJGi026qr2NKHl',
                'pU6IbbgpIuTJEf5kZ7bGfSeUaHt3RhX0',
                'it2PsxzjQC2SpfQaAifPrm8s6BNiY1VP',
                'SLXtXFcZUTh88sBlpxTrYElmVZf3GeEE',
                'LRz5ynd3MqsVZlFH53EDtwFIWncmarnf',
                '8c1U6RREIdmqIBCaQ1D9JxWstGiUT3Xy',
                'PC4R63q3107jjWvFhABQCSAsORdzN4b4',
                'BLxtAHw7IJCgNW3tWayALs1WwaR6eeSw',
                'dgaQ8CKyDdhf7jB6Luh5G7KERXXsucaL',
                'ROHto9n6DIei1yegWsoynrlgvm1M3d0v',
                'rXjBOZj6fwZC17MrwiwyVNvRzvcTujIS',
                'CWHA8Y1osonLndZmAfpLQkwXgbuATLfb',
                'amShsfgpRaSvuLoiFDtUSfDMc5BNUmzO',
                'AXasX1iIrTp43gJ9eXtqqTrcIE9uDZod',
                'kczzFTRJbkGkQ9CYhAecMAXZJ133UDGm',
                'UxFAvvVzHD1FuqiOL4aXRX5AZK3KzwGb',
                '2U2L5qz7274KT0mFQB8LNOjcrs5PiGKZ',
                'i2O5HI4mG1cj0zcmaHpQMVlNA9lEGNB2',
                'gHiElgggrtoxWZvuPAgkgK5XGDOBNLbd',
                'WoUH3XemjlLx0aPS5piHjCFdZoUTBI2t',
                'ppOTNUcNiWJy5PIndj3fF4eOfpziFevq',
                'O9DzCssX6jLpgzPDfcLukN0TGW4y1JaY',
                'AY6lsZFSHniTxFtFTaLJnYb15OEreCaG',
                'Pnoq0uYJFKvAR0FvVdBwD3YFWYIKr4Xr',
                'ZQq6yVdCatmrcySGrxBwCmVgzx1BEGds',
                'WHv7gfQlBHYuLwnMPim9UgiXpXzg9zee',
                'Ne8NKnyzVJaxdwQvOKCXmJaeac2A07fr',
                'AUZMRDPW6xuZy3IbTe6Bchw9lfOVL252',
                'Yg1YkyQGToD87boiUs5K6gYqv3qTBrtJ',
                'fg4iseVz6pz2Iw5pWISzaV6xQjlQupAP',
                'NaVqT6880h1XMcGsGUEb2fNx573r12jY',
                'ssWiiO0RA7ty7szIXCmBKw0v62qcnUvH',
                'YEXjcY6G3lBqlQEU25suzFhl1vNtce59',
                '6nbnb979MKOzODgLR4B2T8cXWVRXEbB0',
                'uDXw2VvqCay9JCncvqcVOj9ANhu5Z2M6',
                '2jMitMpR8eOWj11kexqu50D5a88bFy2H',
                'NBrEEiun2qXU1inTcnXxouCR1iKx8bVb',
                'NC3EK0RyHPhM96w8cVaNMwlWDjkdOQvF',
                'uXqlfix893fgql67600ajRBfPYrI2mBu',
                'slPKzgwICjNVQW5Y0hzu1hWab1RwGE8b',
                '3vyrzOSHLkkbNZBvoa6dpkxQ25OjSj1s',
                'jUemVkCh3Fav7p26x9adFDEcVrjPj8t2',
                'WQTPLATesu6hkDjbjTDQeV7oOoRtuvq0',
                'vBcPEiwcKsFD9G1PyZJYRJKA83UYeiYE',
                'KnNAajFjIZkD7RHAwnI2CKGmL2rDFH71',
                'iNxsf6Gtpfpe3lPwZ0fvFRR9pCgK2SGM',
                'GNKOAjFTrvrEK9o6qpFZGYazz7qolBlc',
                'ORh7CVi3XjIH67n0u9QWqxAVBdA4uXOe',
                'OMkTOuLwT7mv3gytHnZ5zM0RZbcThKfa',
                '74jLRi34MTxRtfGatrHLqPh5DlPb6GzN',
                '06yGPMIGnUqYENnVzLpQ2rlFKPKrTr2E',
                'zWjqwW7s6WyEQD5OHDAopv1DFnenK7Us',
                '7EdAHhZHsYBku8bubwgDCd66eX25DVQ9',
                'Oqu17d0HaVSn2I6hbYry0mzMJls6viP1',
                'FynGNNUlwClVrjjyrkUwobvlYEc4BvXP',
                'AlUtzxphUZ5r0aRnJ2JLAKaEYjzjZ5dj',
                'FKwwDllsuh9Qf7vS2BPcxsiVVdKlpEws',
                '8G3tfqVSFaYKr2MkOFs99ZbwN7e8h3Up',
                'yONTL9Vt8cKs8zUrwspnpIxhVi0xHREO',
                'D7SzpaRwf3v7cEmPL7TzbNzUzZpuFJIA',
                'ai2hjcp6GR1NS2sEirNZzSDgHcOyqA2A',
                'gR11cx6i9rB4lkl6vUm3vgk3h7YJPBtF',
                'urMIALAmGubpcJhdCIP51CCkePKXZWWd',
                'GnU4RaLIqZX9xiAIjoUtC1KLNVKErJDn',
                '4VnxleBQXMxgnEcsETuLG6iFiUVMPj7v',
                'zUCz0lvyUWdsyWfNrCEyAWxMXXMbqFKE',
                'lcXMs8QGRRK1kLBoDVXuN7SOxEMksjuK',
                'b7tYZtmnD21WwnTSosngDr2QYG9fWvxw',
                'VBVJNY9I8z5OjK4P8PlvoBiwUyVmnnF8',
                'jQyx7FnVSQWkUmF1wTjgi9VYRi1TcMU8',
                'eafMZgs21GxmUmvnJshsDMNckRPoVaI0',
                'VvzLDkTTK6NqFhcxkkb39Vim9JHaHAk4',
                'kpSpbNHRWIctNHCcl3jfSSNj2uH7oLBA',
                'ECaXlU5NnolkfVrdW3270njLdQiiKchz',
                'c5VEFNqTRd9zLmWDydI1cRESdCLvKHOS',
                'OPOtCgXbQgoKbjUKc9Ca06uunliU1kiM',
                'qeoUz357HVj3pSZ0JreP68Nnxd6hJnRP',
                'stpQeCGSYYVnqlsh8K28Rw55E8vFZAsj',
                '1lMY8T8SMiWWcq4mPVNJGwPCeFc61k8p',
                'nv8P4KZiPmsDLDkKBWV5O5IeqXK2O3s3',
                'BMd4s141MnRIx30JddywuiWY88ZtoKJK',
                'AZMwujYny67A7kjzrVpqwDJ0lEatZVEH',
                'D72D79Mll6bqY7JnLenrfj4mVDGnIj0V',
                'jVBYHhPUEAmv8bx4m1TIvSW0GA4o03ig',
                'HraRQr1GrYkLyQDRkJhpfIwAZd7eqhvs',
                'zBX5ng3bguMIy9dfjR80hVvOr0kyoqbm',
                'ZgtrOocJ8KEG9JtKHLHha48UZNZWpwx9',
                'sOX1HSzIf5UgMRcdI9skbo8twzJrcv0a',
                'dlRR0dg4H2L9TDChHgTpWXYXMnvglLMY',
                'LWXqzF1hm7SGkbP1qW2xrq93JCcD1x8S',
                'CLQrJ8R779tkFm4JLTxdsZfyW73wC4ph',
                'TL09khlJnqw5RVxzfhxChbbRJgdgNRs2',
                'vQwwgSLM3sMDLQfM5w4KMh5frtTLh46r',
                'XSCdlKdlVK7cVMm90fyffyzPLBVKl0Kj',
                'md087vLQwNQLbbKdvShdWRX76wqkBTpd',
                'kpvkhjHlBTnwpCPwRKyvtKkRTPhqTZ7X',
                'Gq82Bgn4hzjGQsMRDVJ2qdzhvHmyypQB',
            ],
        ],
    ],
];
