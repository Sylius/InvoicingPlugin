<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Entity\InvoiceChannelInterface;

final class InvoiceChannelSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('WEB-US', 'United States');
    }

    public function it_implements_invoice_channel_interface(): void
    {
        $this->shouldImplement(InvoiceChannelInterface::class);
    }

    public function it_has_code(): void
    {
        $this->getCode()->shouldReturn('WEB-US');
    }

    public function it_has_name(): void
    {
        $this->getName()->shouldReturn('United States');
    }
}
