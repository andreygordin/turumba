<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Upload\Validator;

use Imagine\Exception\RuntimeException;
use Imagine\Image\ImagineInterface;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ImageValidator extends ConstraintValidator
{
    private ImagineInterface $imagine;

    public function __construct(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Image) {
            throw new UnexpectedTypeException($constraint, Image::class);
        }

        if (empty($value)) {
            return;
        }

        if (!$value instanceof UploadedFileInterface) {
            throw new UnexpectedTypeException($value, UploadedFileInterface::class);
        }

        try {
            $contents = $value->getStream()->getContents();
            $value->getStream()->rewind();
            $this->imagine->load($contents);
        } catch (RuntimeException $e) {
            $this->context
                ->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
