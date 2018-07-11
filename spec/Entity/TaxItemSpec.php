<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\TaxItemInterface;

final class TaxItemSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('VAT (23%)', 2300);
    }

    function it_implements_tax_item_interface(): void
    {
        $this->shouldImplement(TaxItemInterface::class);
    }

    function it_implements_resource_interface(): void
    {
        $this->shouldImplement(ResourceInterface::class);
    }

    function it_has_proper_tax_item_data(): void
    {
        $this->label()->shouldReturn('VAT (23%)');
        $this->amount()->shouldReturn(2300);
    }

    function it_has_an_invoice(InvoiceInterface $invoice): void
    {
        $this->setInvoice($invoice);
        $this->invoice()->shouldReturn($invoice);
    }
}
