<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\File;

interface TemporaryFileSystemInterface
{
    public function create(string $fileContent, string $filePath): void;
    public function removeFile(string $filePath): void;
}
