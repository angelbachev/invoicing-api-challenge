<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\Currency;

use App\Domain\Model\Currency\Currency;
use App\Tests\Shared\Factory\CurrencyFactory as Factory;
use App\Tests\Unit\UnitTestCase;

final class CurrencyTest extends UnitTestCase
{
    /**
     * @dataProvider provideValidCurrencyData
     */
    public function testConstructorAndGetters(string $code, float $rate, bool $isDefault): void
    {
        $currency = new Currency($code, $rate);

        $this->assertSame($code, $currency->getCode());
        $this->assertSame($rate, $currency->getRate());
        $this->assertSame($isDefault, $currency->isDefault());
    }

    /**
     * @dataProvider provideValidConvertedToData
     */
    public function testConvertTo(Currency $fromCurrency, Currency $toCurrency, float $expectedRate): void
    {
        $this->assertSame($expectedRate, $fromCurrency->convertTo($toCurrency));
    }

    public function provideValidCurrencyData(): array
    {
        return [
            'defaultCurrency' => [Factory::EUR_CODE, Factory::EUR_RATE, true],
            'nonDefaultCurrency' => [Factory::BGN_CODE, Factory::BGN_RATE, false],
        ];
    }

    public function provideValidConvertedToData(): array
    {
        return [
            'sameCurrency' => [Factory::getCurrency(), Factory::getCurrency(), 1.0],
            'toDefaultCurrency' => [
                Factory::getCurrency(Factory::BGN_CODE, Factory::BGN_RATE),
                Factory::getCurrency(),
                0.5102040816326531,
            ],
            'fromDefaultCurrency' => [
                Factory::getCurrency(),
                Factory::getCurrency(Factory::BGN_CODE, Factory::BGN_RATE),
                1.96,
            ],
            'nonDefaultCurrencies' => [
                Factory::getCurrency(Factory::USD_CODE, Factory::USD_RATE),
                Factory::getCurrency(Factory::BGN_CODE, Factory::BGN_RATE),
                1.9858156028368794,
            ],
        ];
    }
}
