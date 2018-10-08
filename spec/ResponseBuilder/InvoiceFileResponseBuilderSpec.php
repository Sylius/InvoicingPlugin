<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\ResponseBuilder;

use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Sylius\InvoicingPlugin\ResponseBuilder\InvoiceFileResponseBuilder;
use Sylius\InvoicingPlugin\ResponseBuilder\InvoiceFileResponseBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

final class InvoiceFileResponseBuilderSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(InvoiceFileResponseBuilder::class);
    }

    function it_implements_invoice_file_response_builder_interface(): void
    {
        $this->shouldImplement(InvoiceFileResponseBuilderInterface::class);
    }

    function it_returns_response_containing_pdf_file_when_its_provided(): void
    {
        $invoicePdf = new InvoicePdf('invoice.pdf', 'invoice_content');

        $response = $this->build(Response::HTTP_OK, $invoicePdf);

        $response->getContent()->shouldBeEqualTo('invoice_content');
        $response->getStatusCode()->shouldBeEqualTo(Response::HTTP_OK);
    }
}
