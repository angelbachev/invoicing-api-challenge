<?php

declare(strict_types=1);

namespace App\Application\Service;

interface CsvReaderInterface
{
    public function read(string $filePath, callable $mapItem): array;
}
