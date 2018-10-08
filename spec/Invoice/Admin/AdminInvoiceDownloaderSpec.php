<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Invoice\Admin;

use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Invoice\Admin\AdminInvoiceDownloaderInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;

final class AdminInvoiceDownloaderSpec extends ObjectBehavior
{
    function let(InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator): void
    {
        $this->beConstructedWith($invoicePdfFileGenerator);
    }

    function it_implements_admin_invoice_downloader_interface(): void
    {
        $this->shouldImplement(AdminInvoiceDownloaderInterface::class);
    }

    function it_returns_pdf_file_with_an_invoice(InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator): void
    {
        $invoicePdf = new InvoicePdf('', '');

        $invoicePdfFileGenerator->generate('0000001')->willReturn($invoicePdf);

        $this->download('0000001')->shouldReturn($invoicePdf);
    }
}
