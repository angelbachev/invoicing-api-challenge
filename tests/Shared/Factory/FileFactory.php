<?php

declare(strict_types=1);

namespace App\Tests\Shared\Factory;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class FileFactory
{
    public const FILE_DATA = 'data.csv';
    public const FILE_INVALID_TYPE = 'invalid-file-type.jpg';
    public const FILE_EMPTY = 'empty.csv';
    public const FILE_TOO_BIG = 'big.csv';
    public const FILE_INVALID_HEADERS = 'invalid-headers.csv';
    public const FILE_INVALID_DOCUMENT_TYPE = 'invalid-document-type.csv';
    public const FILE_MISSING_INVOICE = 'missing-invoice.csv';
    public const FILE_DUPLICATED_DOCUMENTS = 'duplicated-documents.csv';
    public const FILE_EMPTY_DOCUMENT_NUMBER = 'empty-document-number.csv';
    public const FILE_INVOICE_WITH_PARENT = 'invoice-with-parent.csv';
    public const FILE_DUPLICATED_INVOICE = 'duplicated-invoice.csv';

    public const FILES_PATH = __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'File'.DIRECTORY_SEPARATOR;

    public static function getUploadedFile(string $fileName = self::FILE_DATA): UploadedFile
    {
        $tmpDirPath = DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
        // Otherwise, file is moved and not available anymore
        copy(self::FILES_PATH.$fileName, $tmpDirPath.$fileName);

        return new UploadedFile(path: $tmpDirPath.$fileName, originalName: $fileName, test: true);
    }
}
