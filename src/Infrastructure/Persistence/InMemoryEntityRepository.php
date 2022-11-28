<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Model\EntityInterface;

final class InMemoryEntityRepository implements EntityRepositoryInterface
{
    public function __construct(
        private readonly InMemoryPersistence $persistence,
        private readonly string $entityType
    ) {
    }

    public function find(string $id): ?EntityInterface
    {
        return $this->getData()[$id] ?? null;
    }

    /**
     * @param array<string, mixed> $criteria
     */
    public function findBy(array $criteria): ?EntityInterface
    {
        return array_filter(
            $this->getData(),
            function (mixed $entity) use ($criteria) {
                foreach ($criteria as $field => $value) {
                    $method = $this->mapFieldToMethod($field);
                    if ($value !== $entity->$method()) {
                        return false;
                    }
                }

                return true;
            })[0] ?? null;
    }

    /**
     * @return EntityInterface[]
     */
    public function findAll(): array
    {
        return array_values($this->getData());
    }

    private function mapFieldToMethod(string $field): string
    {
        if (str_starts_with($field, 'is')) {
            return $field;
        }

        return 'get'.ucfirst($field);
    }

    private function getData(): array
    {
        return $this->persistence->getData($this->entityType);
    }
}
