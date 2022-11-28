<?php

declare(strict_types=1);

namespace App\Tests\Shared\Factory;

use App\Domain\Model\Currency\Currency;

final class CurrencyFactory
{
    public const DEFAULT_RATE = 1.0;

    public const EUR_CODE = 'EUR';
    public const EUR_RATE = 1.0;

    public const BGN_CODE = 'BGN';
    public const BGN_RATE = 1.96;

    public const USD_CODE = 'USD';
    public const USD_RATE = 0.987;

    public const GBP_CODE = 'GBP';
    public const GBP_RATE = 0.878;

    public const EXCHANGE_RATES = ['EUR:1', 'BGN:1.96', 'USD:0.987', 'GBP:0.878'];

    public static function getCurrency(string $code = self::EUR_CODE, float $rate = self::EUR_RATE): Currency
    {
        return new Currency($code, $rate);
    }
}
