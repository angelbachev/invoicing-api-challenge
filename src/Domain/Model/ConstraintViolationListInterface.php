<?php

declare(strict_types=1);

namespace App\Domain\Model;

interface ConstraintViolationListInterface
{
    public function addError(ConstraintViolationInterface $error): void;

    public function count(): int;

    public function hasErrors(): bool;

    /**
     * @return ConstraintViolationInterface[]
     */
    public function getAll(): array;
}
