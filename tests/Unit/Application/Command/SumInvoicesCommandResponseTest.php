<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Command;

use App\Application\Command\Invoice\SumInvoices\SumInvoicesCommandResponse;
use App\Tests\Shared\Factory\CurrencyFactory;
use App\Tests\Shared\Factory\CustomerFactory;
use App\Tests\Unit\UnitTestCase;

final class SumInvoicesCommandResponseTest extends UnitTestCase
{
    public function testConstructorAndGetters(): void
    {
        $customers = [
            [
                'name' => CustomerFactory::CUSTOMER_1_NAME,
                'balance' => CustomerFactory::CUSTOMER_1_BALANCE_BGN,
            ],
            [
                'name' => CustomerFactory::CUSTOMER_2_NAME,
                'balance' => CustomerFactory::CUSTOMER_2_BALANCE_BGN,
            ],
            [
                'name' => CustomerFactory::CUSTOMER_2_NAME,
                'balance' => CustomerFactory::CUSTOMER_2_BALANCE_BGN,
            ],
        ];
        $response = new SumInvoicesCommandResponse(CurrencyFactory::BGN_CODE, $customers);

        $this->assertSame(CurrencyFactory::BGN_CODE, $response->getCurrency());
        $this->assertSame($customers, $response->getCustomers());
    }
}
