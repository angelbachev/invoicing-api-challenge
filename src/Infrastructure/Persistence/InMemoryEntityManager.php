<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Model\EntityInterface;

final class InMemoryEntityManager implements EntityManagerInterface
{
    public function __construct(private readonly InMemoryPersistence $persistence)
    {
    }

    public function getRepository(string $entityType): InMemoryEntityRepository
    {
        return new InMemoryEntityRepository($this->persistence, $entityType);
    }

    public function persist(EntityInterface $entity): void
    {
        $this->persistence->persist($entity);
    }
}
