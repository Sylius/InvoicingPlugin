<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Provider;

use Gaufrette\Exception\FileNotFound;
use Gaufrette\FilesystemInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Manager\InvoiceFileManagerInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;

final class InvoiceFileProvider implements InvoiceFileProviderInterface
{
    public function __construct(
        private readonly FilesystemInterface $filesystem,
        private readonly InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        private readonly InvoiceFileManagerInterface $invoiceFileManager,
        private readonly string $invoicesDirectory,
        private readonly bool $hasEnabledPdfFileGenerator = true,
    ) {
    }

    public function provide(InvoiceInterface $invoice): InvoicePdf
    {
        $invoiceFileName = $invoice->path();

        try {
            $invoiceFile = $this->filesystem->get($invoiceFileName);
            $invoicePdf = new InvoicePdf($invoiceFileName, $invoiceFile->getContent());
        } catch (FileNotFound) {
            $invoicePdf = $this->invoicePdfFileGenerator->generate($invoice);

            if ($this->hasEnabledPdfFileGenerator) {
                $this->invoiceFileManager->save($invoicePdf);
            }
        }

        $invoicePdf->setFullPath($this->invoicesDirectory . '/' . $invoiceFileName);

        return $invoicePdf;
    }
}
