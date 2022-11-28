<?php

declare(strict_types=1);

namespace App\Tests\Functional\Infrastructure\Delivery\Api\V1\Public\Invoice;

use App\Tests\Functional\FunctionalTestCase;
use App\Tests\Shared\Factory\CurrencyFactory;
use App\Tests\Shared\Factory\CustomerFactory;
use App\Tests\Shared\Factory\FileFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class SumInvoicesActionTest extends FunctionalTestCase
{
    protected const METHOD = 'POST';
    protected const ROUTE = '/api/v1/sumInvoices';

    /**
     * @dataProvider provideValidData
     */
    public function testSumInvoicesReturnSuccessfulResponse(array $params, UploadedFile $file, array $expectedResponse): void
    {
        $this->client->request(method: self::METHOD, uri: self::ROUTE, parameters: $params, files: ['file' => $file]);
        $response = $this->getDecodedJsonResponse();

        $this->assertResponseStatusCodeSame(200);
        $this->assertBalances($expectedResponse, $response);
    }

    /**
     * @dataProvider provideBadRequestData
     */
    public function testSumInvoicesReturnBadRequestResponse(array $params, ?string $file, array $expectedErrors): void
    {
        $files = null !== $file ? ['file' => FileFactory::getUploadedFile($file)] : [];
        $this->client->request(method: self::METHOD, uri: self::ROUTE, parameters: $params, files: $files);
        $response = $this->getDecodedJsonResponse();

        $this->assertResponseStatusCodeSame(400);
        $this->assertMatchesPattern(['errors' => $expectedErrors], $response);
    }

    public function testSumInvoicesReturnNotFoundResponse(): void
    {
        $params = [
            'exchangeRates' => ['EUR:1', 'USD:0.987', 'GBP:0.878'],
            'outputCurrency' => 'EUR',
            'customerVat' => 'test',
        ];
        $file = FileFactory::getUploadedFile();
        $this->client->request(method: self::METHOD, uri: self::ROUTE, parameters: $params, files: ['file' => $file]);
        $this->assertResponseStatusCodeSame(404);
    }

    public function provideValidData(): array
    {
        $params = [
            'exchangeRates' => ['EUR:1', 'USD:0.987', 'GBP:0.878'],
            'outputCurrency' => 'EUR',
        ];

        $file = FileFactory::getUploadedFile();

        return [
            'defaultCurrency' => [
                $params,
                $file,
                [
                    'currency' => CurrencyFactory::EUR_CODE,
                    'customers' => [
                        [
                            'name' => CustomerFactory::CUSTOMER_1_NAME,
                            'balance' => 1962.2160985753,
                        ],
                        [
                            'name' => CustomerFactory::CUSTOMER_2_NAME,
                            'balance' => 697.36575481256,
                        ],
                        [
                            'name' => CustomerFactory::CUSTOMER_3_NAME,
                            'balance' => 1580.6378132118,
                        ],
                    ],
                ],
            ],
            'nonDefaultCurrency' => [
                array_merge($params, ['outputCurrency' => CurrencyFactory::USD_CODE]),
                $file,
                [
                    'currency' => CurrencyFactory::USD_CODE,
                    'customers' => [
                        [
                            'name' => CustomerFactory::CUSTOMER_1_NAME,
                            'balance' => 1936.7072892938497,
                        ],
                        [
                            'name' => CustomerFactory::CUSTOMER_2_NAME,
                            'balance' => 688.3,
                        ],
                        [
                            'name' => CustomerFactory::CUSTOMER_3_NAME,
                            'balance' => 1560.089521640091,
                        ],
                    ],
                ],
            ],
            'forSpecifiedCustomer' => [
                array_merge(
                    $params,
                    [
                        'outputCurrency' => CurrencyFactory::USD_CODE,
                        'customerVat' => CustomerFactory::CUSTOMER_1_VAT_NUMBER,
                    ]
                ),
                $file,
                [
                    'currency' => CurrencyFactory::USD_CODE,
                    'customers' => [
                        [
                            'name' => CustomerFactory::CUSTOMER_1_NAME,
                            'balance' => 1936.7072892938497,
                        ],
                    ],
                ],
            ],
        ];
    }

    public function provideBadRequestData(): array
    {
        $file = FileFactory::FILE_DATA;
        $params = [
            'exchangeRates' => ['EUR:1', 'USD:0.987', 'GBP:0.878'],
            'outputCurrency' => 'EUR',
        ];

        return [
            'emptyBody' => [
                [],
                null,
                [
                    'exchangeRates' => 'This collection should contain 1 element or more.',
                    'outputCurrency' => 'This value should not be blank.',
                    'file' => 'This value should not be blank.',
                ],
            ],
//            'fileIsNotCsv' => [$params, FileFactory::FILE_INVALID_TYPE, ['file' => 'Upload a csv file']], // TODO: Need to be fixed
            'fileIsEmpty' => [$params, FileFactory::FILE_EMPTY, ['file' => 'An empty file is not allowed.']],
            'fileSizeIsInvalid' => [
                $params,
                FileFactory::FILE_TOO_BIG,
                ['file' => 'The file is too large (1.49 MB). Allowed maximum size is 1 MB.'],
            ],
            'invalidHeaders' => [
                $params,
                FileFactory::FILE_INVALID_HEADERS,
                [
                    'customerVatNumber' => 'Customer vat number cannot be empty',
                    'customerName' => 'Customer name cannot be empty',
                ],
            ],
            'unsupportedDocumentCurrency' => [
                array_merge($params, ['exchangeRates' => ['EUR:1', 'BGN:1.96']]),
                $file,
                ['currency' => 'Not supported currency "USD"'],
            ],
            'unsupportedOutputCurrency' => [
                array_merge($params, ['outputCurrency' => 'JPY']),
                $file,
                ['outputCurrency' => 'Output currency is not supported'],
            ],
            'missingDefaultCurrency' => [
                array_merge($params, ['exchangeRates' => ['EUR:1.1', 'USD:0.987', 'GBP:0.878']]),
                $file,
                ['exchangeRates' => 'Default currency is not passed'],
            ],
            'invalidDocumentType' => [
                $params,
                FileFactory::FILE_INVALID_DOCUMENT_TYPE,
                [
                    'type' => 'Invalid document type "5"',
                    'parentDocumentNumber' => 'Parent document number cannot be empty for credit and debit notes',
                ],
            ],
            'missingInvoice' => [
                $params,
                FileFactory::FILE_MISSING_INVOICE,
                ['parentDocumentNumber' => 'Parent document for "1000000260" does not exist'],
            ],
            'emptyDocumentNumber' => [
                $params,
                FileFactory::FILE_EMPTY_DOCUMENT_NUMBER,
                ['documentNumber' => 'Document number cannot be empty'],
            ],
            'invoiceWithParent' => [
                $params,
                FileFactory::FILE_INVOICE_WITH_PARENT,
                ['parentDocumentNumber' => 'Parent document number must be empty for invoice "1000000257"'],
            ],
            'duplicatedDocuments' => [
                $params,
                FileFactory::FILE_DUPLICATED_DOCUMENTS,
                ['documentNumber' => 'Note "1000000260" for invoice "1000000257" exists'],
            ],
            'duplicatedInvoice' => [
                $params,
                FileFactory::FILE_DUPLICATED_INVOICE,
                ['documentNumber' => 'Invoice "1000000257" exists'],
            ],
            'invalidExchangeRates' => [
                array_merge($params, ['exchangeRates' => ['EUR:1', 'WRONG:1.96', 'USD:WRONG']]),
                $file,
                [
                    'exchangeRates.1' => 'This value is not valid.',
                    'exchangeRates.2' => 'This value is not valid.',
                ],
            ],
        ];
    }

    private function assertBalances(array $expectedResponse, array $response): void
    {
        $this->assertSame($expectedResponse['currency'], $response['currency']);
        foreach ($response['customers'] as $key => $customer) {
            $this->assertSame($expectedResponse['customers'][$key]['name'], $customer['name']);
            $this->assertTrue(round($expectedResponse['customers'][$key]['balance'], 8) === round($customer['balance'], 8));
        }
    }
}
