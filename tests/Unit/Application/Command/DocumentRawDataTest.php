<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Command;

use App\Application\Command\Invoice\SumInvoices\DocumentRawData;
use App\Domain\Model\Invoice\DocumentType;
use App\Tests\Shared\Factory\CurrencyFactory;
use App\Tests\Shared\Factory\CustomerFactory;
use App\Tests\Shared\Factory\InvoiceFactory;
use App\Tests\Unit\UnitTestCase;

final class DocumentRawDataTest extends UnitTestCase
{
    public function testGetters(): void
    {
        $data = [
            'Customer' => CustomerFactory::CUSTOMER_1_NAME,
            'Vat number' => CustomerFactory::CUSTOMER_1_VAT_NUMBER,
            'Document number' => InvoiceFactory::NUMBER_1,
            'Type' => DocumentType::Invoice->value,
            'Currency' => CurrencyFactory::EUR_CODE,
            'Total' => InvoiceFactory::TOTAL_1,
            'Parent document' => '',
        ];

        $documentRawData = DocumentRawData::fromArray($data);

        $this->assertSame($data['Customer'], $documentRawData->getCustomerName());
        $this->assertSame($data['Vat number'], $documentRawData->getCustomerVatNumber());
        $this->assertSame($data['Document number'], $documentRawData->getDocumentNumber());
        $this->assertSame($data['Type'], $documentRawData->getType());
        $this->assertSame($data['Currency'], $documentRawData->getCurrency());
        $this->assertSame($data['Total'], $documentRawData->getTotal());
        $this->assertSame($data['Parent document'], $documentRawData->getParentDocumentNumber());
    }
}
