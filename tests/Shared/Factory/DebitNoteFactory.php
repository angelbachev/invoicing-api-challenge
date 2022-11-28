<?php

declare(strict_types=1);

namespace App\Tests\Shared\Factory;

use App\Domain\Model\Invoice\DebitNote;

final class DebitNoteFactory
{
    public const NUMBER_1 = '1000000261';
    public const INVOICE_1 = '1';
    public const CURRENCY_1 = ['code' => CurrencyFactory::GBP_CODE, 'rate' => CurrencyFactory::GBP_RATE];
    public const TOTAL_1 = 50.0;

    public const NUMBER_2 = '1000000263';
    public const INVOICE_2 = '3';
    public const CURRENCY_2 = ['code' => CurrencyFactory::EUR_CODE, 'rate' => CurrencyFactory::EUR_RATE];
    public const TOTAL_2 = 100.0;

    public static function getDebitNote(
        string $number = self::NUMBER_1,
        string $invoiceId = self::INVOICE_1,
        array $currency = self::CURRENCY_1,
        float $total = self::TOTAL_1,
    ): DebitNote {
        $getInvoiceMethod = 'getInvoice'.$invoiceId;

        return new DebitNote(
            number: $number,
            invoice: InvoiceFactory::$getInvoiceMethod(),
            currency: CurrencyFactory::getCurrency($currency['code'], $currency['rate']),
            total: $total,
        );
    }

    public static function getDebitNote1(): DebitNote
    {
        return self::getDebitNote();
    }

    public static function getDebitNote2(): DebitNote
    {
        return self::getDebitNote(self::NUMBER_2, self::INVOICE_2, self::CURRENCY_2, self::TOTAL_2);
    }
}
