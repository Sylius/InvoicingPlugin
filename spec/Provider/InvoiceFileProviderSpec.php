<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Provider;

use Gaufrette\File;
use Gaufrette\FilesystemInterface;
use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceFileNameGeneratorInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Sylius\InvoicingPlugin\Provider\InvoiceFileProviderInterface;

final class InvoiceFileProviderSpec extends ObjectBehavior
{
    function let(
        InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator,
        FilesystemInterface $filesystem
    ): void {
        $this->beConstructedWith($invoiceFileNameGenerator, $filesystem, '/path/to/invoices');
    }

    function it_implements_invoice_file_provider_interface(): void
    {
        $this->shouldImplement(InvoiceFileProviderInterface::class);
    }

    function it_provides_invoice_file_for_invoice(
        InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator,
        FilesystemInterface $filesystem,
        InvoiceInterface $invoice,
        File $invoiceFile
    ): void {
        $invoiceFileNameGenerator->generateForPdf($invoice)->willReturn('invoice.pdf');
        $filesystem->get('invoice.pdf')->willReturn($invoiceFile);

        $invoiceFile->getContent()->willReturn('CONTENT');

        $invoicePdf = new InvoicePdf('invoice.pdf', 'CONTENT');
        $invoicePdf->setFullPath('/path/to/invoices/invoice.pdf');

        $this->provide($invoice)->shouldBeLike($invoicePdf);
    }
}
