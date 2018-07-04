<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\File;

interface TemporaryFilePathGeneratorInterface
{
    public function generate(string $filePathPattern, string...$parameters): string;
    public function removeFile(string $filePath): void;
}
