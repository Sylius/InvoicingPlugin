<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Saver;

use Gaufrette\FilesystemInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;

final class InvoiceFileSaver implements InvoiceFileSaverInterface
{
    /** @var FilesystemInterface */
    private $filesystem;

    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function save(InvoicePdf $file): void
    {
        $this->filesystem->write($file->filename(), $file->content());
    }
}
