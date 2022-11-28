<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Model;

use App\Domain\Model\ConstraintViolationInterface;
use App\Domain\Model\ConstraintViolationListInterface;

final class ConstraintViolationList implements ConstraintViolationListInterface
{
    /** @param ConstraintViolationInterface[] $errors */
    public function __construct(private array $errors = [])
    {
    }

    public function count(): int
    {
        return count($this->errors);
    }

    public function hasErrors(): bool
    {
        return $this->count() > 0;
    }

    /**
     * @return ConstraintViolationInterface[]
     */
    public function getAll(): array
    {
        return $this->errors;
    }

    public function addError(ConstraintViolationInterface $error): void
    {
        $this->errors[] = $error;
    }
}
