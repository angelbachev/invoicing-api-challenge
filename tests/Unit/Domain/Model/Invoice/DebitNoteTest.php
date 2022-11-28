<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\Invoice;

use App\Domain\Model\Invoice\DebitNote;
use App\Tests\Shared\Factory\CurrencyFactory;
use App\Tests\Shared\Factory\DebitNoteFactory;
use App\Tests\Shared\Factory\InvoiceFactory;
use App\Tests\Unit\UnitTestCase;

final class DebitNoteTest extends UnitTestCase
{
    public function testConstructorAndGetters(): void
    {
        $invoice = InvoiceFactory::getInvoice();
        $currency = CurrencyFactory::getCurrency();
        $note = new DebitNote(DebitNoteFactory::NUMBER_1, $invoice, $currency, DebitNoteFactory::TOTAL_1);

        $this->assertSame(DebitNoteFactory::NUMBER_1, $note->getNumber());
        $this->assertSame($invoice, $note->getInvoice());
        $this->assertSame($currency, $note->getCurrency());
        $this->assertSame(DebitNoteFactory::TOTAL_1, $note->getTotal());
    }
}
