<?php

declare(strict_types=1);

namespace App\Tests\Shared\Factory;

use App\Infrastructure\Domain\Model\ConstraintViolation;

final class ConstraintViolationFactory
{
    public const PATH = 'path';
    public const MESSAGE = 'message';
    public const CODE = 400;
    public const NOT_FOUND_CODE = 404;

    public static function getConstraintViolation(
        string $path = self::PATH,
        string $message = self::MESSAGE,
    ): ConstraintViolation {
        return new ConstraintViolation($path, $message);
    }
}
