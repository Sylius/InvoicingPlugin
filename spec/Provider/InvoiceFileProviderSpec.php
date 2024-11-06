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

namespace spec\Sylius\InvoicingPlugin\Provider;

use Gaufrette\Exception\FileNotFound;
use Gaufrette\File;
use Gaufrette\FilesystemInterface;
use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceFileNameGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Manager\InvoiceFileManagerInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Sylius\InvoicingPlugin\Provider\InvoiceFileProviderInterface;

final class InvoiceFileProviderSpec extends ObjectBehavior
{
    function let(
        InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator,
        FilesystemInterface $filesystem,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        InvoiceFileManagerInterface $invoiceFileManager,
    ): void {
        $this->beConstructedWith(
            $invoiceFileNameGenerator,
            $filesystem,
            $invoicePdfFileGenerator,
            $invoiceFileManager,
            '/path/to/invoices',
        );
    }

    function it_implements_invoice_file_provider_interface(): void
    {
        $this->shouldImplement(InvoiceFileProviderInterface::class);
    }

    function it_provides_invoice_file_for_invoice(
        InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator,
        FilesystemInterface $filesystem,
        InvoiceInterface $invoice,
        File $invoiceFile,
    ): void {
        $invoiceFileNameGenerator->generateForPdf($invoice)->willReturn('invoice.pdf');
        $filesystem->get('invoice.pdf')->willReturn($invoiceFile);

        $invoiceFile->getContent()->willReturn('CONTENT');

        $invoicePdf = new InvoicePdf('invoice.pdf', 'CONTENT');
        $invoicePdf->setFullPath('/path/to/invoices/invoice.pdf');

        $this->provide($invoice)->shouldBeLike($invoicePdf);
    }

    function it_generates_invoice_if_it_does_not_exist_and_provides_it(
        InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator,
        FilesystemInterface $filesystem,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        InvoiceFileManagerInterface $invoiceFileManager,
        InvoiceInterface $invoice,
        File $invoiceFile,
    ): void {
        $invoiceFileNameGenerator->generateForPdf($invoice)->willReturn('invoice.pdf');

        $filesystem->get('invoice.pdf')->willThrow(FileNotFound::class);

        $invoicePdf = new InvoicePdf('invoice.pdf', 'CONTENT');
        $invoicePdf->setFullPath('/path/to/invoices/invoice.pdf');

        $invoicePdfFileGenerator->generate($invoice)->willReturn($invoicePdf);
        $invoiceFileManager->save($invoicePdf)->shouldBeCalled();

        $this->provide($invoice)->shouldBeLike($invoicePdf);
    }
}
