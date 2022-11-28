<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\Customer;

use App\Domain\Model\Currency\Currency;
use App\Domain\Model\Customer\Customer;
use App\Domain\Model\Invoice\Invoice;
use App\Tests\Shared\Factory\CreditNoteFactory;
use App\Tests\Shared\Factory\CurrencyFactory;
use App\Tests\Shared\Factory\CustomerFactory;
use App\Tests\Shared\Factory\DebitNoteFactory;
use App\Tests\Shared\Factory\InvoiceFactory;
use App\Tests\Unit\UnitTestCase;

final class CustomerTest extends UnitTestCase
{
    public function testConstructorAndGetters(): void
    {
        $customer = new Customer(CustomerFactory::CUSTOMER_1_VAT_NUMBER, CustomerFactory::CUSTOMER_1_NAME);

        $this->assertSame(CustomerFactory::CUSTOMER_1_VAT_NUMBER, $customer->getVatNumber());
        $this->assertSame(CustomerFactory::CUSTOMER_1_NAME, $customer->getName());
        $this->assertCount(0, $customer->getInvoices());
    }

    public function testAddInvoice(): void
    {
        $customer = CustomerFactory::getCustomer();
        $invoice = InvoiceFactory::getInvoice();
        $customer->addInvoice($invoice);

        $invoices = $customer->getInvoices();
        $this->assertCount(1, $invoices);
        $this->assertSame($invoice, $invoices[0]);
        $this->assertTrue($customer->hasInvoice($invoice));
    }

    /**
     * @dataProvider provideCalculateBalanceInCurrencyData
     */
    public function testCalculateBalanceInCurrency(Currency $convertedCurrency, float $convertedAmount): void
    {
        $customer = CustomerFactory::getCustomer();
        $invoice1 = new Invoice(
            InvoiceFactory::NUMBER_1,
            $customer,
            CurrencyFactory::getCurrency(InvoiceFactory::CURRENCY_1['code'], InvoiceFactory::CURRENCY_1['rate']),
            InvoiceFactory::TOTAL_1,
        );
        $invoice1->addCreditNote(
            CreditNoteFactory::NUMBER_1,
            CurrencyFactory::getCurrency(CreditNoteFactory::CURRENCY_1['code'], CreditNoteFactory::CURRENCY_1['rate']),
            CreditNoteFactory::TOTAL_1,
        );
        $invoice1->addDebitNote(
            DebitNoteFactory::NUMBER_1,
            CurrencyFactory::getCurrency(DebitNoteFactory::CURRENCY_1['code'], DebitNoteFactory::CURRENCY_1['rate']),
            DebitNoteFactory::TOTAL_1,
        );
        $customer->addInvoice($invoice1);

        $invoice2 = new Invoice(
            InvoiceFactory::NUMBER_4,
            $customer,
            CurrencyFactory::getCurrency(InvoiceFactory::CURRENCY_4['code'], InvoiceFactory::CURRENCY_4['rate']),
            InvoiceFactory::TOTAL_4,
        );
        $customer->addInvoice($invoice2);

        $this->assertSame($convertedAmount, $customer->calculateBalanceInCurrency($convertedCurrency));
    }

    public function provideCalculateBalanceInCurrencyData(): array
    {
        return [
            'defaultCurrency' => [CurrencyFactory::getCurrency(), 1962.216098575329],
            'nonDefaultCurrency' => [CurrencyFactory::getCurrency(CurrencyFactory::BGN_CODE, CurrencyFactory::BGN_RATE), 3845.9435532076445],
        ];
    }
}
