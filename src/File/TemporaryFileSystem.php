<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\File;

final class TemporaryFileSystem implements TemporaryFileSystemInterface
{
    public function create(string $fileContent, string $filePath): void
    {
        $file = fopen($filePath, 'w');

        fwrite($file, $fileContent);

        fclose($file);
    }

    public function removeFile(string $filePath): void
    {
        unlink($filePath);
    }
}
