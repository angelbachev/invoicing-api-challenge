<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Domain\Model\Customer;

use App\Infrastructure\Domain\Model\Customer\InMemoryCustomerRepository;
use App\Tests\Integration\IntegrationTestCase;
use App\Tests\Shared\Factory\CustomerFactory;

final class InMemoryCustomerRepositoryTest extends IntegrationTestCase
{
    private InMemoryCustomerRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var InMemoryCustomerRepository $repository */
        $repository = self::getContainer()->get(InMemoryCustomerRepository::class);
        $this->repository = $repository;
    }

    public function testMethods(): void
    {
        $customer = CustomerFactory::getCustomer();

        $this->assertCount(0, $this->repository->findAll());
        $this->assertNull($this->repository->findOneByVatNumber($customer->getVatNumber()));

        $this->repository->save($customer);
        $this->assertCount(1, $this->repository->findAll());
        $this->assertSame($customer, $this->repository->findOneByVatNumber($customer->getVatNumber()));

        $this->repository->save($customer);
        $this->assertCount(1, $this->repository->findAll());
        $this->assertSame($customer, $this->repository->findOneByVatNumber($customer->getVatNumber()));

        $customer = CustomerFactory::getCustomer(CustomerFactory::CUSTOMER_2_VAT_NUMBER, CustomerFactory::CUSTOMER_2_NAME);
        $this->repository->save($customer);
        $this->assertCount(2, $this->repository->findAll());
        $this->assertSame($customer, $this->repository->findOneByVatNumber($customer->getVatNumber()));
    }
}
