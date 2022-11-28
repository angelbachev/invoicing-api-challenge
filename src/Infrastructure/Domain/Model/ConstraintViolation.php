<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Model;

use App\Domain\Model\ConstraintViolationInterface;

final class ConstraintViolation implements ConstraintViolationInterface
{
    public function __construct(
        private readonly string $path,
        private readonly string $message,
        private readonly int $code = 400
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getCode(): int
    {
        return $this->code;
    }
}
