<?php

declare(strict_types=1);

namespace App\Application\Command\Invoice\SumInvoices;

final class DocumentRawData
{
    public function __construct(
        private readonly string $customerName,
        private readonly string $customerVatNumber,
        private readonly string $documentNumber,
        private readonly int $type,
        private readonly string $currency,
        private readonly float $total,
        private readonly ?string $parentDocumentNumber,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            customerName: $data['Customer'] ?? '',
            customerVatNumber: $data['Vat number'] ?? '',
            documentNumber: $data['Document number'] ?? '',
            type: (int) ($data['Type'] ?? ''),
            currency: $data['Currency'] ?? '',
            total: (float) ($data['Total'] ?? ''),
            parentDocumentNumber: $data['Parent document'] ?? '',
        );
    }

    public function getCustomerName(): string
    {
        return $this->customerName;
    }

    public function getCustomerVatNumber(): string
    {
        return $this->customerVatNumber;
    }

    public function getDocumentNumber(): string
    {
        return $this->documentNumber;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getParentDocumentNumber(): ?string
    {
        return $this->parentDocumentNumber;
    }

    public function getType(): int
    {
        return $this->type;
    }
}
