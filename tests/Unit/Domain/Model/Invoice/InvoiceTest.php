<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\Invoice;

use App\Domain\Model\Currency\Currency;
use App\Domain\Model\Invoice\DocumentType;
use App\Domain\Model\Invoice\Invoice;
use App\Tests\Shared\Factory\CreditNoteFactory;
use App\Tests\Shared\Factory\CurrencyFactory;
use App\Tests\Shared\Factory\CustomerFactory;
use App\Tests\Shared\Factory\DebitNoteFactory;
use App\Tests\Shared\Factory\InvoiceFactory;
use App\Tests\Unit\UnitTestCase;

final class InvoiceTest extends UnitTestCase
{
    public function testConstructorAndGetters(): void
    {
        $customer = CustomerFactory::getCustomer();
        $currency = CurrencyFactory::getCurrency();
        $invoice = new Invoice(InvoiceFactory::NUMBER_1, $customer, $currency, InvoiceFactory::TOTAL_1);

        $this->assertSame(InvoiceFactory::NUMBER_1, $invoice->getNumber());
        $this->assertSame($customer, $invoice->getCustomer());
        $this->assertSame($currency, $invoice->getCurrency());
        $this->assertSame(InvoiceFactory::TOTAL_1, $invoice->getTotal());
        $this->assertCount(0, $invoice->getCreditNotes());
        $this->assertCount(0, $invoice->getDebitNotes());
    }

    public function testAddCreditNote(): void
    {
        $currency = CurrencyFactory::getCurrency();
        $invoice = InvoiceFactory::getInvoice();

        $this->assertCount(0, $invoice->getCreditNotes());

        $invoice->addCreditNote(CreditNoteFactory::NUMBER_1, $currency, CreditNoteFactory::TOTAL_1);

        $this->assertCount(1, $invoice->getCreditNotes());
    }

    public function testAddDebitNote(): void
    {
        $currency = CurrencyFactory::getCurrency();
        $invoice = InvoiceFactory::getInvoice();

        $this->assertCount(0, $invoice->getDebitNotes());

        $invoice->addDebitNote(DebitNoteFactory::NUMBER_1, $currency, DebitNoteFactory::TOTAL_1);

        $this->assertCount(1, $invoice->getDebitNotes());
    }

    /**
     * @dataProvider provideHasNoteData
     */
    public function testHasNote(string $number, int $type, int $searchType): void
    {
        $currency = CurrencyFactory::getCurrency();
        $invoice = InvoiceFactory::getInvoice();

        $this->assertFalse($invoice->hasNote($number, $searchType));
        if (DocumentType::CreditNote->value === $type) {
            $invoice->addCreditNote($number, $currency, $invoice->getTotal());
        } else {
            $invoice->addDebitNote($number, $currency, $invoice->getTotal());
        }
    }

    /**
     * @dataProvider provideSumCreditNotesInCurrencyData
     */
    public function testSumCreditNotesInCurrency(Currency $convertedCurrency, float $convertedAmount): void
    {
        $invoice = InvoiceFactory::getInvoice();
        $invoice->addCreditNote(
            CreditNoteFactory::NUMBER_1,
            CurrencyFactory::getCurrency(CreditNoteFactory::CURRENCY_1['code'], CreditNoteFactory::CURRENCY_1['rate']),
            CreditNoteFactory::TOTAL_1,
        );
        $invoice->addDebitNote(
            DebitNoteFactory::NUMBER_1,
            CurrencyFactory::getCurrency(DebitNoteFactory::CURRENCY_1['code'], DebitNoteFactory::CURRENCY_1['rate']),
            DebitNoteFactory::TOTAL_1,
        );

        $this->assertSame($convertedAmount, $invoice->calculateTotalInCurrency($convertedCurrency));
    }

    public function provideHasNoteData(): array
    {
        return [
            'creditNote' => [CreditNoteFactory::NUMBER_1, DocumentType::CreditNote->value, DocumentType::CreditNote->value],
            'debitNote' => [DebitNoteFactory::NUMBER_1, DocumentType::DebitNote->value, DocumentType::DebitNote->value],
            'noteTypeMismatch' => [DebitNoteFactory::NUMBER_1, DocumentType::DebitNote->value, DocumentType::DebitNote->value],
            'invalidType' => [DebitNoteFactory::NUMBER_1, DocumentType::DebitNote->value, DocumentType::Invoice->value],
        ];
    }

    public function provideSumCreditNotesInCurrencyData(): array
    {
        return [
            'defaultCurrency' => [CurrencyFactory::getCurrency(), 362.216098575329],
            'nonDefaultCurrency' => [CurrencyFactory::getCurrency(CurrencyFactory::BGN_CODE, CurrencyFactory::BGN_RATE), 709.9435532076446],
        ];
    }
}
