<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\File;

final class TemporaryFilePathGenerator implements TemporaryFilePathGeneratorInterface
{
    public function generate(string $filePathPattern, string...$parameters): string
    {
        return sys_get_temp_dir() . '/' . vsprintf($filePathPattern, $parameters);
    }

    public function removeFile(string $filePath): void
    {
        unlink($filePath);
    }
}
