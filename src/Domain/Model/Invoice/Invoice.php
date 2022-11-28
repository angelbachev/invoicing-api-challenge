<?php

declare(strict_types=1);

namespace App\Domain\Model\Invoice;

use App\Domain\Model\Currency\Currency;
use App\Domain\Model\Customer\Customer;
use App\Domain\Model\EntityInterface;

final class Invoice implements EntityInterface
{
    private readonly string $number;

    private readonly Customer $customer;

    private readonly Currency $currency;

    private readonly float $total;

    /**
     * @var DebitNote[]
     */
    private array $debitNotes = [];

    /**
     * @var CreditNote[]
     */
    private array $creditNotes = [];

    public function __construct(string $number, Customer $customer, Currency $currency, float $total)
    {
        $this->number = $number;
        $this->customer = $customer;
        $this->currency = $currency;
        $this->total = $total;

        $this->customer->addInvoice($this);
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function addCreditNote(string $number, Currency $currency, float $total): self
    {
        $this->creditNotes[] = new CreditNote($number, $this, $currency, $total);

        return $this;
    }

    public function getCreditNotes(): array
    {
        return $this->creditNotes;
    }

    public function addDebitNote(string $number, Currency $currency, float $total): self
    {
        $this->debitNotes[] = new DebitNote($number, $this, $currency, $total);

        return $this;
    }

    public function getDebitNotes(): array
    {
        return $this->debitNotes;
    }

    public function calculateTotalInCurrency(Currency $currency): float
    {
        $total = $this->total * $this->currency->convertTo($currency);

        return $total + $this->sumDebitNotesInCurrency($currency) - $this->sumCreditNotesInCurrency($currency);
    }

    public function hasNote(string $number, int $type): bool
    {
        if (DocumentType::CreditNote->value === $type) {
            return 0 < count(array_filter($this->creditNotes, fn (CreditNote $note) => $note->getNumber() === $number));
        }

        if (DocumentType::DebitNote->value === $type) {
            return 0 < count(array_filter($this->debitNotes, fn (DebitNote $note) => $note->getNumber() === $number));
        }

        return false;
    }

    private function sumDebitNotesInCurrency(Currency $currency): float
    {
        $total = 0.0;
        foreach ($this->debitNotes as $note) {
            $total += $note->getTotal() * $note->getCurrency()->convertTo($currency);
        }

        return $total;
    }

    private function sumCreditNotesInCurrency(Currency $currency): float
    {
        $total = 0.0;
        foreach ($this->creditNotes as $note) {
            $total += $note->getTotal() * $note->getCurrency()->convertTo($currency);
        }

        return $total;
    }
}
