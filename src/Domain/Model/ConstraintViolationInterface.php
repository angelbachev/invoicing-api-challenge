<?php

declare(strict_types=1);

namespace App\Domain\Model;

interface ConstraintViolationInterface
{
    public function getMessage(): string;

    public function getPath(): string;

    public function getCode(): int;
}
