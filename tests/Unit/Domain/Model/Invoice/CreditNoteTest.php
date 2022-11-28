<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\Invoice;

use App\Domain\Model\Invoice\CreditNote;
use App\Tests\Shared\Factory\CreditNoteFactory;
use App\Tests\Shared\Factory\CurrencyFactory;
use App\Tests\Shared\Factory\InvoiceFactory;
use App\Tests\Unit\UnitTestCase;

final class CreditNoteTest extends UnitTestCase
{
    public function testConstructorAndGetters(): void
    {
        $invoice = InvoiceFactory::getInvoice();
        $currency = CurrencyFactory::getCurrency();
        $note = new CreditNote(CreditNoteFactory::NUMBER_1, $invoice, $currency, CreditNoteFactory::TOTAL_1);

        $this->assertSame(CreditNoteFactory::NUMBER_1, $note->getNumber());
        $this->assertSame($invoice, $note->getInvoice());
        $this->assertSame($currency, $note->getCurrency());
        $this->assertSame(CreditNoteFactory::TOTAL_1, $note->getTotal());
    }
}
