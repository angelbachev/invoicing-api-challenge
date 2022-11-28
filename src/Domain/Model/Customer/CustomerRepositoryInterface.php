<?php

declare(strict_types=1);

namespace App\Domain\Model\Customer;

interface CustomerRepositoryInterface
{
    public function findOneByVatNumber(string $vatNumber): ?Customer;

    /**
     * @return Customer[]
     */
    public function findAll(): array;

    public function save(Customer $customer): void;
}
