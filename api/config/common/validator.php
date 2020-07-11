<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

use App\Http\Validator\ConflictViolationDetector;
use App\Module\Page\Validator\PageExists;
use App\Module\Page\Validator\PathIsNotTaken;
use App\Validator\ConstraintValidatorFactory;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Psr\Container\ContainerInterface;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

return [
    ValidatorInterface::class => function (ContainerInterface $container): ValidatorInterface {
        /** @psalm-suppress DeprecatedMethod */
        AnnotationRegistry::registerLoader('class_exists');

        /** @var ConstraintValidatorFactory $validatorFactory */
        $validatorFactory = $container->get(ConstraintValidatorFactory::class);

        return Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->setConstraintValidatorFactory($validatorFactory)
            ->getValidator();
    },

    ConstraintValidatorFactory::class => function (ContainerInterface $container): ConstraintValidatorFactory {
        return new ConstraintValidatorFactory(
            function (string $className) use ($container): ConstraintValidatorInterface {
                /** @var ConstraintValidatorInterface $validator */
                $validator = $container->get($className);
                return $validator;
            }
        );
    },

    ConflictViolationDetector::class => DI\autowire(ConflictViolationDetector::class)
        ->constructor(
            function (ContainerInterface $container): array {
                /**
                 * @psalm-suppress MixedArrayAccess
                 * @psalm-var array{
                 *     conflictViolationCodes:array<string>,
                 * } $config
                 */
                $config = $container->get('config')['validator'];
                return $config['conflictViolationCodes'];
            }
        ),

    'config' => [
        'validator' => [
            'conflictViolationCodes' => [
                PageExists::PAGE_DOESNT_EXIST_ERROR,
                PathIsNotTaken::PATH_IS_TAKEN_ERROR,
            ],
        ],
    ],
];
