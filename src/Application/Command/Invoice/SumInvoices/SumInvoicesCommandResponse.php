<?php

declare(strict_types=1);

namespace App\Application\Command\Invoice\SumInvoices;

final class SumInvoicesCommandResponse
{
    /**
     * @param array<array{name: string, balance: float}> $customers
     */
    public function __construct(private readonly string $currency, private readonly array $customers)
    {
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getCustomers(): array
    {
        return $this->customers;
    }
}
