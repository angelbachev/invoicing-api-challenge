<?php

declare(strict_types=1);

namespace App\Domain\Model\Invoice;

interface InvoiceRepositoryInterface
{
    public function findOneByNumber(string $number): ?Invoice;

    /**
     * @return Invoice[]
     */
    public function findAll(): array;

    public function save(Invoice $invoice): void;
}
