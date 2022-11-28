<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Model\Customer;

use App\Domain\Model\Customer\Customer;
use App\Domain\Model\Customer\CustomerRepositoryInterface;
use App\Infrastructure\Persistence\EntityManagerInterface;
use App\Infrastructure\Persistence\EntityRepositoryInterface;

final class InMemoryCustomerRepository implements CustomerRepositoryInterface
{
    private readonly EntityManagerInterface $entityManager;
    private readonly EntityRepositoryInterface $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Customer::class);
    }

    public function findOneByVatNumber(string $vatNumber): ?Customer
    {
        /** @var ?Customer $customer */
        $customer = $this->repository->find($vatNumber);

        return $customer;
    }

    /**
     * @return Customer[]
     */
    public function findAll(): array
    {
        /** @var Customer[] $customers */
        $customers = $this->repository->findAll();

        return $customers;
    }

    public function save(Customer $customer): void
    {
        $this->entityManager->persist($customer);
    }
}
