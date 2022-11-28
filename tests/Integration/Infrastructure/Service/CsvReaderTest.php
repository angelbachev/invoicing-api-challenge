<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure\Service;

use App\Infrastructure\Service\CsvReader;
use App\Tests\Integration\IntegrationTestCase;
use App\Tests\Shared\Factory\FileFactory;

final class CsvReaderTest extends IntegrationTestCase
{
    private CsvReader $reader;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var CsvReader $reader */
        $reader = self::getContainer()->get(CsvReader::class);
        $this->reader = $reader;
    }

    public function testReadWithValidFile(): void
    {
        $file = FileFactory::getUploadedFile();
        $data = $this->reader->read(
            (string) $file->getRealPath(),
            fn (array $line) => ['vatNumber' => $line['Vat number']]
        );

        $expectedData = [
            ['vatNumber' => '@string@'],
            '@array_previous_repeat@',
        ];
        $this->assertMatchesPattern($expectedData, $data);
    }

    public function testReadWithMissingFile(): void
    {
        $data = $this->reader->read('missing-file', fn (array $line) => ['vatNumber' => $line['Vat number']]);

        $this->assertCount(0, $data);
    }

    public function testReadWithMissingHeaders(): void
    {
        $file = FileFactory::getUploadedFile(FileFactory::FILE_EMPTY);
        $data = $this->reader->read(
            (string) $file->getRealPath(),
            fn (array $line) => ['vatNumber' => $line['Vat number']]
        );

        $this->assertCount(0, $data);
    }
}
