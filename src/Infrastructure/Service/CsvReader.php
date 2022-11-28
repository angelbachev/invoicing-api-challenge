<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Application\Service\CsvReaderInterface;

final class CsvReader implements CsvReaderInterface
{
    public function read(string $filePath, callable $mapItem): array
    {
        if (!file_exists($filePath)) {
            return [];
        }

        /** @var array $file */
        $file = file($filePath, FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES);

        $headersLine = array_shift($file);
        if (null === $headersLine) {
            return [];
        }

        /** @var string[] $headers */
        $headers = str_getcsv($headersLine);
        $documents = [];
        foreach ($file as $dataLine) {
            $data = str_getcsv($dataLine);
            $documents[] = $mapItem(array_combine($headers, $data));
        }

        return $documents;
    }
}
