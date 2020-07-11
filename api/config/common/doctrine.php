<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

use App\Data\Doctrine\MonologSQLLogger;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Logging\SQLLogger;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\Setup;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;

return [
    EventManager::class => function (ContainerInterface $container): EventManager {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var string[] $subscribers
         */
        $subscribers = $container->get('config')['doctrine']['subscribers'];
        $eventManager = new EventManager();
        foreach ($subscribers as $name) {
            /** @var EventSubscriber $subscriber */
            $subscriber = $container->get($name);
            $eventManager->addEventSubscriber($subscriber);
        }
        return $eventManager;
    },

    Connection::class => function (ContainerInterface $container): Connection {
        /** @var EventManager $eventManager */
        $eventManager = $container->get(EventManager::class);

        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{
         *     driver: string,
         *     host: string,
         *     user: string,
         *     password: string,
         *     dbname: string,
         *     charset: string,
         * } $settings
         */
        $settings = $container->get('config')['doctrine']['connection'];

        $config = new Configuration();
        if ($container->has(SQLLogger::class)) {
            /** @var SQLLogger $sqlLogger */
            $sqlLogger = $container->get(SQLLogger::class);
            $config->setSQLLogger($sqlLogger);
        }

        return DriverManager::getConnection($settings, $config, $eventManager);
    },

    EntityManagerInterface::class => function (ContainerInterface $container): EntityManagerInterface {
        /** @var Connection $connection */
        $connection = $container->get(Connection::class);

        /** @var EventManager $eventManager */
        $eventManager = $container->get(EventManager::class);

        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{
         *     metadataDirs:array,
         *     devMode:bool,
         *     proxyDir:string,
         *     cacheDir:?string,
         *     logFile:?string,
         *     types:array<string,string>,
         *     connection:array,
         *     subscribers:array<string>
         * } $settings
         */
        $settings = $container->get('config')['doctrine'];

        $config = Setup::createAnnotationMetadataConfiguration(
            $settings['metadataDirs'],
            $settings['devMode'],
            $settings['proxyDir'],
            $settings['cacheDir'] ? new FilesystemCache($settings['cacheDir']) : new ArrayCache(),
            false
        );

        $config->setNamingStrategy(new UnderscoreNamingStrategy());

        foreach ($settings['types'] as $name => $class) {
            if (!Type::hasType($name)) {
                Type::addType($name, $class);
            }
        }

        if ($container->has(SQLLogger::class)) {
            /** @var SQLLogger $sqlLogger */
            $sqlLogger = $container->get(SQLLogger::class);
            $config->setSQLLogger($sqlLogger);
        }

        return EntityManager::create($connection, $config, $eventManager);
    },

    SQLLogger::class => function (ContainerInterface $container) {
        /**
         * @psalm-suppress MixedArrayAccess
         * @psalm-var array{
         *     metadataDirs:array,
         *     devMode:bool,
         *     proxyDir:string,
         *     cacheDir:?string,
         *     logFile:?string,
         *     types:array<string,string>,
         *     connection:array,
         *     subscribers:array<string>
         * } $config
         */
        $config = $container->get('config')['doctrine'];
        if (!isset($config['logFile'])) {
            return null;
        }
        $logger = new Logger('doctrine');
        $logger->pushHandler(new StreamHandler($config['logFile'], Logger::DEBUG));
        return new MonologSQLLogger($logger);
    },

    'config' => [
        'doctrine' => [
            'devMode' => false,
            'cacheDir' => __DIR__ . '/../../var/cache/' . PHP_SAPI . '/doctrine/cache',
            'proxyDir' => __DIR__ . '/../../var/cache/' . PHP_SAPI . '/doctrine/proxy',
            'logFile' => null,
            'connection' => [
                'driver' => 'pdo_pgsql',
                'host' => getenv('DB_HOST'),
                'user' => getenv('DB_USER'),
                'password' => getenv('DB_PASSWORD'),
                'dbname' => getenv('DB_NAME'),
                'charset' => 'utf-8',
            ],
            'subscribers' => [],
            'metadataDirs' => [
                __DIR__ . '/../../src/Module/Page/Entity',
            ],
            'types' => [
                App\Data\Doctrine\NullableStringType::NAME => App\Data\Doctrine\NullableStringType::class,
                App\Module\Page\Entity\Page\IdType::NAME => App\Module\Page\Entity\Page\IdType::class,
                App\Module\Page\Entity\Page\FullNameType::NAME => App\Module\Page\Entity\Page\FullNameType::class,
                App\Module\Page\Entity\Page\NoteType::NAME => App\Module\Page\Entity\Page\NoteType::class,
                App\Module\Page\Entity\Page\ShortNameType::NAME => App\Module\Page\Entity\Page\ShortNameType::class,
                App\Module\Page\Entity\Page\TitleType::NAME => App\Module\Page\Entity\Page\TitleType::class,
            ],
        ],
    ],
];
