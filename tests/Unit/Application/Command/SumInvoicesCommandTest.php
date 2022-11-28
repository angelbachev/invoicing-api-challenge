<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Command;

use App\Application\Command\Invoice\SumInvoices\SumInvoicesCommand;
use App\Tests\Shared\Factory\CurrencyFactory;
use App\Tests\Shared\Factory\CustomerFactory;
use App\Tests\Shared\Factory\FileFactory;
use App\Tests\Unit\UnitTestCase;

final class SumInvoicesCommandTest extends UnitTestCase
{
    public function testConstructorAndGetters(): void
    {
        $file = FileFactory::getUploadedFile();
        $command = new SumInvoicesCommand(
            exchangeRates: CurrencyFactory::EXCHANGE_RATES,
            outputCurrency: CurrencyFactory::BGN_CODE,
            file: $file,
            customerVat: CustomerFactory::CUSTOMER_1_VAT_NUMBER,
        );

        $this->assertMatchesPattern(CurrencyFactory::EXCHANGE_RATES, $command->getExchangeRates());
        $this->assertSame(CurrencyFactory::BGN_CODE, $command->getOutputCurrency());
        $this->assertSame($file, $command->getFile());
        $this->assertSame(CustomerFactory::CUSTOMER_1_VAT_NUMBER, $command->getCustomerVat());
    }
}
