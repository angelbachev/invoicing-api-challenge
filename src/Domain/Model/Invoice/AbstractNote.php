<?php

declare(strict_types=1);

namespace App\Domain\Model\Invoice;

use App\Domain\Model\Currency\Currency;

abstract class AbstractNote
{
    public function __construct(
        protected readonly string $number,
        protected readonly Invoice $invoice,
        protected readonly Currency $currency,
        protected readonly float $total
    ) {
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }
}
