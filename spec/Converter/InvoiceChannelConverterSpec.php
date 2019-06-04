<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Converter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\InvoicingPlugin\Converter\InvoiceChannelConverterInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceChannelInterface;

final class InvoiceChannelConverterSpec extends ObjectBehavior
{
    function it_implements_invoice_channel_converter_interface(): void
    {
        $this->shouldImplement(InvoiceChannelConverterInterface::class);
    }

    function it_converts_channel_to_invoice_channel(ChannelInterface $channel): void
    {
        $channel->getCode()->willReturn('en_US');
        $channel->getName()->willReturn('United States');

        $this->convert($channel)->shouldReturnAnInstanceOf(InvoiceChannelInterface::class);
    }
}
