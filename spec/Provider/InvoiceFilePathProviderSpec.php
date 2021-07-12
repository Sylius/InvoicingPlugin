<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceFileNameGeneratorInterface;
use Sylius\InvoicingPlugin\Provider\InvoiceFilePathProviderInterface;

final class InvoiceFilePathProviderSpec extends ObjectBehavior
{
    function let(InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator): void
    {
        $this->beConstructedWith($invoiceFileNameGenerator, '/files/invoices');
    }

    function it_implements_invoice_file_path_provider_interface(): void
    {
        $this->shouldImplement(InvoiceFilePathProviderInterface::class);
    }

    function it_provides_path_to_generated_invoice_file_based_on_its_name(
        InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator,
        InvoiceInterface $invoice
    ): void {
        $invoiceFileNameGenerator->generateForPdf($invoice)->willReturn('invoice.pdf');

        $this->provide($invoice)->shouldReturn('/files/invoices/invoice.pdf');
    }
}
