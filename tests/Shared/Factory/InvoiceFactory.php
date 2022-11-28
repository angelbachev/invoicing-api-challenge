<?php

declare(strict_types=1);

namespace App\Tests\Shared\Factory;

use App\Domain\Model\Invoice\Invoice;

final class InvoiceFactory
{
    public const NUMBER_1 = '1000000257';
    public const CUSTOMER_1 = ['vatNumber' => CustomerFactory::CUSTOMER_1_VAT_NUMBER, 'name' => CustomerFactory::CUSTOMER_1_NAME];
    public const CURRENCY_1 = ['code' => CurrencyFactory::USD_CODE, 'rate' => CurrencyFactory::USD_RATE];
    public const TOTAL_1 = 400.0;

    public const NUMBER_2 = '1000000258';
    public const CUSTOMER_2 = ['vatNumber' => CustomerFactory::CUSTOMER_2_VAT_NUMBER, 'name' => CustomerFactory::CUSTOMER_2_NAME];
    public const CURRENCY_2 = ['code' => CurrencyFactory::EUR_CODE, 'rate' => CurrencyFactory::EUR_RATE];
    public const TOTAL_2 = 900.0;

    public const NUMBER_3 = '1000000259';
    public const CUSTOMER_3 = ['vatNumber' => CustomerFactory::CUSTOMER_3_VAT_NUMBER, 'name' => CustomerFactory::CUSTOMER_3_NAME];
    public const CURRENCY_3 = ['code' => CurrencyFactory::GBP_CODE, 'rate' => CurrencyFactory::GBP_RATE];
    public const TOTAL_3 = 1300.0;

    public const NUMBER_4 = '1000000264';
    public const CUSTOMER_4 = ['vatNumber' => CustomerFactory::CUSTOMER_3_VAT_NUMBER, 'name' => CustomerFactory::CUSTOMER_3_NAME];
    public const CURRENCY_4 = ['code' => CurrencyFactory::EUR_CODE, 'rate' => CurrencyFactory::EUR_RATE];
    public const TOTAL_4 = 1600.0;

    public static function getInvoice(
        string $number = self::NUMBER_1,
        array $customer = self::CUSTOMER_1,
        array $currency = self::CURRENCY_1,
        float $total = self::TOTAL_1,
    ): Invoice {
        return new Invoice(
            number: $number,
            customer: CustomerFactory::getCustomer($customer['vatNumber'], $customer['name']),
            currency: CurrencyFactory::getCurrency($currency['code'], $currency['rate']),
            total: $total,
        );
    }

    public static function getInvoice1(): Invoice
    {
        return self::getInvoice();
    }

    public static function getInvoice2(): Invoice
    {
        return self::getInvoice(self::NUMBER_2, self::CUSTOMER_2, self::CURRENCY_2, self::TOTAL_2);
    }

    public static function getInvoice3(): Invoice
    {
        return self::getInvoice(self::NUMBER_3, self::CUSTOMER_3, self::CURRENCY_3, self::TOTAL_3);
    }

    public static function getInvoice4(): Invoice
    {
        return self::getInvoice(self::NUMBER_4, self::CUSTOMER_4, self::CURRENCY_4, self::TOTAL_4);
    }
}
