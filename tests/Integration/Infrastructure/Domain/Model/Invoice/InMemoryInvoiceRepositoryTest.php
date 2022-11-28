<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Domain\Model\Invoice;

use App\Infrastructure\Domain\Model\Invoice\InMemoryInvoiceRepository;
use App\Tests\Integration\IntegrationTestCase;
use App\Tests\Shared\Factory\InvoiceFactory;

final class InMemoryInvoiceRepositoryTest extends IntegrationTestCase
{
    private InMemoryInvoiceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var InMemoryInvoiceRepository $repository */
        $repository = self::getContainer()->get(InMemoryInvoiceRepository::class);
        $this->repository = $repository;
    }

    public function testMethods(): void
    {
        $invoice = InvoiceFactory::getInvoice();

        $this->assertCount(0, $this->repository->findAll());
        $this->assertNull($this->repository->findOneByNumber($invoice->getNumber()));

        $this->repository->save($invoice);
        $this->assertCount(1, $this->repository->findAll());
        $this->assertSame($invoice, $this->repository->findOneByNumber($invoice->getNumber()));

        $this->repository->save($invoice);
        $this->assertCount(1, $this->repository->findAll());
        $this->assertSame($invoice, $this->repository->findOneByNumber($invoice->getNumber()));

        $invoice = InvoiceFactory::getInvoice2();
        $this->repository->save($invoice);
        $this->assertCount(2, $this->repository->findAll());
        $this->assertSame($invoice, $this->repository->findOneByNumber($invoice->getNumber()));
    }
}
