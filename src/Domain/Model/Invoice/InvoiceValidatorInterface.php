<?php

declare(strict_types=1);

namespace App\Domain\Model\Invoice;

use App\Application\Command\Invoice\SumInvoices\DocumentRawData;
use App\Domain\Model\ConstraintViolationInterface;
use App\Domain\Model\ConstraintViolationListInterface;

interface InvoiceValidatorInterface
{
    public function validate(array $data): ConstraintViolationListInterface;

    public function validateOutputCurrency(string $outputCurrency, array $exchangeRates): ?ConstraintViolationInterface;

    public function validateDocumentRawData(DocumentRawData $data): ConstraintViolationListInterface;
}
