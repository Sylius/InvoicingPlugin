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
use Sylius\PdfGenerationBundle\Core\Filesystem\Manager\PdfFileManagerInterface;
use Sylius\PdfGenerationBundle\Core\Model\PdfFile;
use Webmozart\Assert\Assert;

final class InvoiceFileProvider implements InvoiceFileProviderInterface
{
    public function __construct(
        private readonly FilesystemInterface|PdfFileManagerInterface $filesystem,
        private readonly InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        private readonly ?InvoiceFileManagerInterface $invoiceFileManager = null,
        private readonly ?string $invoicesDirectory = null,
        private readonly bool $hasEnabledPdfFileGenerator = true,
    ) {
        if ($this->filesystem instanceof FilesystemInterface) {
            trigger_deprecation(
                'sylius/invoicing-plugin',
                '2.2',
                'Passing an instance of %s to %s is deprecated and it will not be supported in 3.0, use an instance of %s instead.',
                FilesystemInterface::class,
                self::class,
                PdfFileManagerInterface::class,
            );
        }
        if (null !== $this->invoiceFileManager) {
            trigger_deprecation(
                'sylius/invoicing-plugin',
                '2.2',
                'Passing a value for $invoiceFileManager argument to %s is deprecated and the argument will be removed in 3.0.',
                self::class,
            );
        }
        if (null !== $this->invoicesDirectory) {
            trigger_deprecation(
                'sylius/invoicing-plugin',
                '2.2',
                'Passing a value for $invoicesDirectory argument to %s is deprecated and the argument will be removed in 3.0.',
                self::class,
            );
        }
    }

    public function provide(InvoiceInterface $invoice): InvoicePdf
    {
        $invoiceFileName = $invoice->path();

        if ($this->filesystem instanceof PdfFileManagerInterface) {
            return $this->provideUsingPdfBundle($invoiceFileName, $invoice);
        }

        return $this->provideUsingLegacyGaufrette($invoiceFileName, $invoice);
    }

    private function provideUsingPdfBundle(string $invoiceFileName, InvoiceInterface $invoice): InvoicePdf
    {
        Assert::isInstanceOf($this->filesystem, PdfFileManagerInterface::class);

        if ($this->filesystem->has($invoiceFileName, 'sylius_invoicing')) {
            $pdfFile = $this->filesystem->get($invoiceFileName, 'sylius_invoicing');
            $invoicePdf = new InvoicePdf($pdfFile->filename(), $pdfFile->content());
        } else {
            $invoicePdf = $this->invoicePdfFileGenerator->generate($invoice);

            if (!$this->hasEnabledPdfFileGenerator) {
                return $invoicePdf;
            }

            $pdfFile = new PdfFile($invoicePdf->filename(), $invoicePdf->content());
            $this->filesystem->save($pdfFile, 'sylius_invoicing');
        }

        $invoicePdf->setFullPath(
            $this->filesystem->resolveLocalPath($invoiceFileName, 'sylius_invoicing'),
        );

        return $invoicePdf;
    }

    private function provideUsingLegacyGaufrette(string $invoiceFileName, InvoiceInterface $invoice): InvoicePdf
    {
        Assert::isInstanceOf($this->filesystem, FilesystemInterface::class);
        Assert::isInstanceOf($this->invoiceFileManager, InvoiceFileManagerInterface::class);

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
