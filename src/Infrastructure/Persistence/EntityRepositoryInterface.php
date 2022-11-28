<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Model\EntityInterface;

interface EntityRepositoryInterface
{
    public function find(string $id): ?EntityInterface;

    /**
     * @param array<string, mixed> $criteria
     */
    public function findBy(array $criteria): ?EntityInterface;

    /**
     * @return EntityInterface[]
     */
    public function findAll(): array;
}
