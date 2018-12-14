<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\InvoicingPlugin\Twig\FindChannelByInvoiceChannelCodeExtensionInterface;

final class FindChannelByInvoiceChannelCodeExtensionSpec extends ObjectBehavior
{
    public function let(ChannelRepositoryInterface $channelRepository): void
    {
        $this->beConstructedWith($channelRepository);
    }

    public function it_implements_find_channel_by_invoice_channel_code_extension_interface(): void
    {
        $this->shouldImplement(FindChannelByInvoiceChannelCodeExtensionInterface::class);
    }

    public function it_finds_channel_by_invoice_channel_code(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel
    ): void {
        $channelRepository->findOneByCode('en_US')->willReturn($channel);

        $this->findByInvoiceChannelCode('en_US')->shouldReturn($channel);
    }
}
