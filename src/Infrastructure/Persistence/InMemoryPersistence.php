<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Model\Currency\Currency;
use App\Domain\Model\Customer\Customer;
use App\Domain\Model\EntityInterface;
use App\Domain\Model\Invoice\Invoice;

final class InMemoryPersistence implements PersistenceInterface
{
    /**
     * @var array<string, Currency>
     */
    private array $currencies = [];
    /**
     * @var array<string,Customer>
     */
    private array $customers = [];

    /**
     * @var array<string,Invoice>
     */
    private array $invoices = [];

    public function persist(EntityInterface $entity): void
    {
        switch (get_class($entity)) {
            case Currency::class:
                /* @var Currency $entity */
                $this->currencies[$entity->getCode()] = $entity;
                break;
            case Customer::class:
                /* @var Customer $entity */
                $this->customers[$entity->getVatnumber()] = $entity;
                break;
            case Invoice::class:
                /* @var Invoice $entity */
                $this->customers[$entity->getCustomer()->getVatNumber()] = $entity->getCustomer();
                $this->invoices[$entity->getNumber()] = $entity;
                break;
            default:
            // TODO:
            }
    }

    /**
     * @return array<string, EntityInterface>
     */
    public function getData(string $type): array
    {
        if (Currency::class === $type) {
            return $this->currencies;
        }

        if (Customer::class === $type) {
            return $this->customers;
        }

        if (Invoice::class === $type) {
            return $this->invoices;
        }

        // TODO: throws error
        return [];
    }
}
