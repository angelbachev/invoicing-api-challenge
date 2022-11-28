<?php

declare(strict_types=1);

namespace App\Tests\Shared\Factory;

use App\Domain\Model\Invoice\CreditNote;

final class CreditNoteFactory
{
    public const NUMBER_1 = '1000000260';
    public const INVOICE_1 = '1';
    public const CURRENCY_1 = ['code' => CurrencyFactory::EUR_CODE, 'rate' => CurrencyFactory::EUR_RATE];
    public const TOTAL_1 = 100.0;

    public const NUMBER_2 = '1000000262';
    public const INVOICE_2 = '2';
    public const CURRENCY_2 = ['code' => CurrencyFactory::USD_CODE, 'rate' => CurrencyFactory::USD_RATE];
    public const TOTAL_2 = 200.0;

    public static function getCreditNote(
        string $number = self::NUMBER_1,
        string $invoiceId = self::INVOICE_1,
        array $currency = self::CURRENCY_1,
        float $total = self::TOTAL_1,
    ): CreditNote {
        $getInvoiceMethod = 'getInvoice'.$invoiceId;

        return new CreditNote(
            number: $number,
            invoice: InvoiceFactory::$getInvoiceMethod(),
            currency: CurrencyFactory::getCurrency($currency['code'], $currency['rate']),
            total: $total,
        );
    }

    public static function getCreditNote1(): CreditNote
    {
        return self::getCreditNote();
    }

    public static function getCreditNote2(): CreditNote
    {
        return self::getCreditNote(self::NUMBER_2, self::INVOICE_2, self::CURRENCY_2, self::TOTAL_2);
    }
}
