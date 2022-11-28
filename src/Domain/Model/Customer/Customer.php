<?php

declare(strict_types=1);

namespace App\Domain\Model\Customer;

use App\Domain\Model\Currency\Currency;
use App\Domain\Model\EntityInterface;
use App\Domain\Model\Invoice\Invoice;

final class Customer implements EntityInterface
{
    private readonly string $vatNumber;

    private readonly string $name;

    /**
     * @var Invoice[]
     */
    private array $invoices = [];

    public function __construct(string $vatNumber, string $name)
    {
        $this->vatNumber = $vatNumber;
        $this->name = $name;
    }

    public function getVatNumber(): string
    {
        return $this->vatNumber;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addInvoice(Invoice $invoice): void
    {
        if (!$this->hasInvoice($invoice)) {
            $this->invoices[] = $invoice;
        }
    }

    public function getInvoices(): array
    {
        return $this->invoices;
    }

    public function hasInvoice(Invoice $invoice): bool
    {
        return count(
            array_filter(
                $this->invoices,
                fn (Invoice $existingInvoice) => $existingInvoice->getNumber() === $invoice->getNumber()
            )
        ) > 0;
    }

    public function calculateBalanceInCurrency(Currency $currency): float
    {
        $total = 0.0;
        foreach ($this->invoices as $invoice) {
            $total += $invoice->calculateTotalInCurrency($currency);
        }

        return $total;
    }
}
