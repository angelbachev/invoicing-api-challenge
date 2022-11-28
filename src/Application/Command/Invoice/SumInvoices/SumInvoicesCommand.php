<?php

declare(strict_types=1);

namespace App\Application\Command\Invoice\SumInvoices;

use App\Application\Command\AbstractCommand;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class SumInvoicesCommand extends AbstractCommand
{
    public function __construct(
        private readonly array $exchangeRates,
        private readonly string $outputCurrency,
        private readonly ?UploadedFile $file,
        private readonly ?string $customerVat,
    ) {
    }

    public function getExchangeRates(): array
    {
        return $this->exchangeRates;
    }

    public function getOutputCurrency(): string
    {
        return $this->outputCurrency;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function getCustomerVat(): ?string
    {
        return $this->customerVat;
    }
}
