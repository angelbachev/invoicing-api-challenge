<?php

declare(strict_types=1);

namespace App\Tests\Shared\Factory;

use App\Domain\Model\Customer\Customer;

final class CustomerFactory
{
    public const CUSTOMER_1_VAT_NUMBER = '123456789';
    public const CUSTOMER_1_NAME = 'Vendor 1';
    public const CUSTOMER_1_BALANCE_BGN = 3845.9435532076445;

    public const CUSTOMER_2_VAT_NUMBER = '987654321';
    public const CUSTOMER_2_NAME = 'Vendor 2';
    public const CUSTOMER_2_BALANCE_BGN = 1366.836879432624;

    public const CUSTOMER_3_VAT_NUMBER = '123465123';
    public const CUSTOMER_3_NAME = 'Vendor 3';
    public const CUSTOMER_3_BALANCE_BGN = 3098.0501138952163;

    public static function getCustomer(
        string $vatNumber = self::CUSTOMER_1_VAT_NUMBER,
        string $name = self::CUSTOMER_1_NAME
    ): Customer {
        return new Customer($vatNumber, $name);
    }
}
