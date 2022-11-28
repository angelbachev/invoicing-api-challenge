<?php

declare(strict_types=1);

namespace App\Infrastructure\Domain\Model\Invoice;

use App\Application\Command\Invoice\SumInvoices\DocumentRawData;
use App\Domain\Model\ConstraintViolationInterface;
use App\Domain\Model\ConstraintViolationListInterface;
use App\Domain\Model\Currency\CurrencyRepositoryInterface;
use App\Domain\Model\Invoice\DocumentType;
use App\Domain\Model\Invoice\InvoiceRepositoryInterface;
use App\Domain\Model\Invoice\InvoiceValidatorInterface;
use App\Infrastructure\Domain\Model\ConstraintViolation;
use App\Infrastructure\Domain\Model\ConstraintViolationList;
use App\Infrastructure\Validation\Constraint\DefaultCurrencyConstraint;
use App\Infrastructure\Validation\Constraint\NoDuplicateExchangeRatesConstraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface as SymfonyConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface as SymfonyValidatorInterface;

final class InvoiceValidator implements InvoiceValidatorInterface
{
    public function __construct(
        private readonly SymfonyValidatorInterface $validator,
        private readonly CurrencyRepositoryInterface $currencyRepository,
        private readonly InvoiceRepositoryInterface $invoiceRepository,
    ) {
    }

    public function validate(array $data): ConstraintViolationListInterface
    {
        $errors = $this->validator->validate(
            $data,
            new Assert\Collection(
                fields: [
                    'exchangeRates' => [
                        new Assert\Sequentially([
                            new Assert\Count(min: 1),
                            new Assert\All([
                                new Assert\Sequentially([
                                    new Assert\Type('string'),
                                    new Assert\Regex(pattern: '/^([\w]){3}:\d*(.\d+)*$/'),
                                ]),
                            ]),
                            new NoDuplicateExchangeRatesConstraint(),
                            new DefaultCurrencyConstraint(),
                        ]),
                    ],
                    'outputCurrency' => [
                        new Assert\NotBlank(),
                        new Assert\Regex(pattern: '/^([\w]){3}$/'),
                    ],
                    'customerVat' => [],
                    'file' => [
                        new Assert\NotBlank(),
                        new Assert\File(maxSize: '1M'),
                    ],
                ],
            )
        );

        return $this->formatErrors($errors);
    }

    public function validateOutputCurrency(string $outputCurrency, array $exchangeRates): ?ConstraintViolationInterface
    {
        $isOutputCurrencySupported = count(
            array_filter(
                $exchangeRates,
                fn (string $exchangeRate) => $outputCurrency === substr($exchangeRate, 0, 3)
            )
        ) > 0;

        if (!$isOutputCurrencySupported) {
            return new ConstraintViolation('outputCurrency', 'Output currency is not supported');
        }

        return null;
    }

    public function validateDocumentRawData(DocumentRawData $data): ConstraintViolationListInterface
    {
        $errors = new ConstraintViolationList();

        if ('' === $data->getCustomerVatNumber()) {
            $errors->addError(new ConstraintViolation('customerVatNumber', 'Customer vat number cannot be empty'));
        }

        if ('' === $data->getCustomerName()) {
            $errors->addError(new ConstraintViolation('customerName', 'Customer name cannot be empty'));
        }

        if ('' === $data->getDocumentNumber()) {
            $errors->addError(new ConstraintViolation('documentNumber', 'Document number cannot be empty'));
        }

        $type = $data->getType();
        if (null === DocumentType::tryFrom($type)) {
            $errors->addError(new ConstraintViolation('type', sprintf('Invalid document type "%d"', $type)));
        }

        if ('' !== $data->getParentDocumentNumber() && DocumentType::Invoice->value === $type) {
            $errors->addError(
                new ConstraintViolation(
                    'parentDocumentNumber',
                    sprintf('Parent document number must be empty for invoice "%s"', $data->getDocumentNumber())
                )
            );
        }

        if ('' === $data->getParentDocumentNumber() && DocumentType::Invoice->value !== $type) {
            $errors->addError(
                new ConstraintViolation(
                    'parentDocumentNumber',
                    'Parent document number cannot be empty for credit and debit notes'
                )
            );
        }

        if (null === $this->currencyRepository->findOneByCode($data->getCurrency())) {
            $errors->addError(
                new ConstraintViolation('currency', sprintf('Not supported currency "%s"', $data->getCurrency()))
            );
        }

        if ($errors->hasErrors()) {
            return $errors;
        }

        if (
            DocumentType::Invoice->value === $type
            && null !== $this->invoiceRepository->findOneByNumber($data->getDocumentNumber())
        ) {
            $errors->addError(
                new ConstraintViolation('documentNumber', sprintf('Invoice "%s" exists', $data->getDocumentNumber()))
            );
        }

        if (DocumentType::Invoice->value !== $type) {
            $invoice = $this->invoiceRepository->findOneByNumber((string) $data->getParentDocumentNumber());
            if (null === $invoice) {
                $errors->addError(
                    new ConstraintViolation(
                        'parentDocumentNumber',
                        sprintf('Parent document for "%s" does not exist', $data->getDocumentNumber())
                    )
                );
            } elseif ($invoice->hasNote($data->getDocumentNumber(), $data->getType())) {
                $errors->addError(
                    new ConstraintViolation(
                        'documentNumber',
                        sprintf(
                            'Note "%s" for invoice "%s" exists',
                            $data->getDocumentNumber(),
                            $data->getParentDocumentNumber()
                        )
                    )
                );
            }
        }

        return $errors;
    }

    private function formatErrors(SymfonyConstraintViolationListInterface $errors): ConstraintViolationListInterface
    {
        $response = new ConstraintViolationList();
        if (0 === $errors->count()) {
            return $response;
        }

        foreach ($errors as $error) {
            /** @var string $path */
            $path = preg_replace(['/\]\[/', '/\[/', '/\]/'], ['.', '', ''], $error->getPropertyPath());
            /** @var string $message */
            $message = $error->getMessage();
            $response->addError(new ConstraintViolation($path, $message));
        }

        return $response;
    }
}
