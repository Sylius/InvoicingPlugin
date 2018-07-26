<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;

final class LineItemSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(
            'Mjolnir',
            2,
            5000,
            10000,
            300,
            10300,
            'Blue',
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353'
        );
    }

    function it_implements_line_item_interface(): void
    {
        $this->shouldImplement(LineItemInterface::class);
    }

    function it_implements_resource_interface(): void
    {
        $this->shouldImplement(ResourceInterface::class);
    }

    function it_has_proper_line_item_data(): void
    {
        $this->name()->shouldReturn('Mjolnir');
        $this->quantity()->shouldReturn(2);
        $this->unitPrice()->shouldReturn(5000);
        $this->subtotal()->shouldReturn(10000);
        $this->taxTotal()->shouldReturn(300);
        $this->total()->shouldReturn(10300);
        $this->variantName()->shouldReturn('Blue');
        $this->variantCode()->shouldReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');
    }

    function it_has_an_invoice(InvoiceInterface $invoice): void
    {
        $this->setInvoice($invoice);
        $this->invoice()->shouldReturn($invoice);
    }
}
