<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Model\EntityInterface;

interface PersistenceInterface
{
    public function persist(EntityInterface $entity): void;

    /**
     * @return array<string, EntityInterface>
     */
    public function getData(string $type): array;
}
