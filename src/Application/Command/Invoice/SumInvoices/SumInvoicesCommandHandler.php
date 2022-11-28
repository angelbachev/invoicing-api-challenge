<?php

declare(strict_types=1);

namespace App\Application\Command\Invoice\SumInvoices;

use App\Application\Service\CsvReaderInterface;
use App\Application\Shared\ErrorResponse;
use App\Domain\Model\Currency\Currency;
use App\Domain\Model\Currency\CurrencyRepositoryInterface;
use App\Domain\Model\Customer\Customer;
use App\Domain\Model\Customer\CustomerRepositoryInterface;
use App\Domain\Model\Invoice\DocumentType;
use App\Domain\Model\Invoice\Invoice;
use App\Domain\Model\Invoice\InvoiceRepositoryInterface;
use App\Domain\Model\Invoice\InvoiceValidatorInterface;

final class SumInvoicesCommandHandler
{
    public function __construct(
        private readonly InvoiceValidatorInterface $validator,
        private readonly CsvReaderInterface $csvReader,
        private readonly CurrencyRepositoryInterface $currencyRepository,
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly CustomerRepositoryInterface $customerRepository,
    ) {
    }

    public function __invoke(SumInvoicesCommand $command): SumInvoicesCommandResponse|ErrorResponse
    {
        $errors = $this->validator->validate($command->toArray());
        if ($errors->hasErrors()) {
            return ErrorResponse::fromErrors($errors);
        }

        $outputCurrencyError = $this->validator->validateOutputCurrency($command->getOutputCurrency(), $command->getExchangeRates());
        if (null !== $outputCurrencyError) {
            return ErrorResponse::fromCustomError($outputCurrencyError);
        }

        $this->saveCurrencies($command->getExchangeRates());

        $errors = $this->saveDocuments($this->readDocuments((string) $command->getFile()?->getRealPath()));
        if (null !== $errors) {
            return $errors;
        }

        return $this->getBalances($command->getOutputCurrency(), $command->getCustomerVat());
    }

    private function saveCurrencies(array $exchangeRates): void
    {
        foreach ($exchangeRates as $exchangeRate) {
            [$code, $rate] = explode(':', $exchangeRate);
            $currency = new Currency($code, (float) $rate);
            $this->currencyRepository->save($currency);
        }
    }

    /**
     * @return DocumentRawData[]
     */
    private function readDocuments(string $filePath): array
    {
        /** @var DocumentRawData[] $documents */
        $documents = $this->csvReader->read($filePath, fn (array $data) => DocumentRawData::fromArray($data));
        usort($documents, fn (DocumentRawData $doc1, DocumentRawData $doc2) => $doc1->getType() <=> $doc2->getType());

        return $documents;
    }

    /**
     * @param DocumentRawData[] $documents
     */
    private function saveDocuments(array $documents): ?ErrorResponse
    {
        foreach ($documents as $document) {
            $errors = $this->validator->validateDocumentRawData($document);
            if ($errors->hasErrors()) {
                return ErrorResponse::fromErrors($errors);
            }

            $customer = $this->customerRepository->findOneByVatNumber($document->getCustomerVatNumber());
            if (null === $customer) {
                $customer = new Customer($document->getCustomerVatNumber(), $document->getCustomerName());
                $this->customerRepository->save($customer);
            }
            /** @var Currency $currency */
            $currency = $this->currencyRepository->findOneByCode($document->getCurrency());
            if (DocumentType::Invoice->value === $document->getType()) {
                $invoice = new Invoice($document->getDocumentNumber(), $customer, $currency, $document->getTotal());
            } else {
                /** @var Invoice $invoice */
                $invoice = $this->invoiceRepository->findOneByNumber((string) $document->getParentDocumentNumber());
                if (DocumentType::CreditNote->value === $document->getType()) {
                    $invoice->addCreditNote($document->getDocumentNumber(), $currency, $document->getTotal());
                } else {
                    $invoice->addDebitNote($document->getDocumentNumber(), $currency, $document->getTotal());
                }
            }
            $this->invoiceRepository->save($invoice);
        }

        return null;
    }

    private function getBalances(string $outputCurrencyCode, ?string $customerVat): SumInvoicesCommandResponse|ErrorResponse
    {
        /** @var Currency $outputCurrency */
        $outputCurrency = $this->currencyRepository->findOneByCode($outputCurrencyCode);
        if (null !== $customerVat && '' !== $customerVat) {
            $customer = $this->customerRepository->findOneByVatNumber($customerVat);
            if (null === $customer) {
                return ErrorResponse::notFound();
            }

            return new SumInvoicesCommandResponse(
                $outputCurrency->getCode(),
                [
                    [
                        'name' => $customer->getName(),
                        'balance' => $customer->calculateBalanceInCurrency($outputCurrency),
                    ],
                ]
            );
        }

        $customers = $this->customerRepository->findAll();
        $balances = [];
        foreach ($customers as $customer) {
            $balances[] = [
                'name' => $customer->getName(),
                'balance' => $customer->calculateBalanceInCurrency($outputCurrency),
            ];
        }

        return new SumInvoicesCommandResponse($outputCurrency->getCode(), $balances);
    }
}
