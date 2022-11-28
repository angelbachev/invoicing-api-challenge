<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Model\EntityInterface;

interface EntityManagerInterface
{
    public function getRepository(string $entityType): EntityRepositoryInterface;

    public function persist(EntityInterface $entity): void;
}
