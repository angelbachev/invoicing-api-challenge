<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Model\Invoice;

use App\Domain\Model\Invoice\Invoice;
use App\Domain\Model\Invoice\InvoiceRepositoryInterface;
use App\Infrastructure\Persistence\EntityManagerInterface;
use App\Infrastructure\Persistence\EntityRepositoryInterface;

final class InMemoryInvoiceRepository implements InvoiceRepositoryInterface
{
    private readonly EntityManagerInterface $entityManager;
    private readonly EntityRepositoryInterface $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Invoice::class);
    }

    public function findOneByNumber(string $number): ?Invoice
    {
        /** @var ?Invoice $invoice */
        $invoice = $this->repository->find($number);

        return $invoice;
    }

    /**
     * @return Invoice[]
     */
    public function findAll(): array
    {
        /** @var Invoice[] $invoices */
        $invoices = $this->repository->findAll();

        return $invoices;
    }

    public function save(Invoice $invoice): void
    {
        $this->entityManager->persist($invoice);
    }
}
