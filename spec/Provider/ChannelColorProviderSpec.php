<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\InvoicingPlugin\Provider\ChannelColorProviderInterface;

final class ChannelColorProviderSpec extends ObjectBehavior
{
    public function let(ChannelRepositoryInterface $channelRepository): void
    {
        $this->beConstructedWith($channelRepository);
    }

    public function it_implements_channel_color_provider_interface(): void
    {
        $this->shouldImplement(ChannelColorProviderInterface::class);
    }

    public function it_returns_channel_color(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel
    ): void {
        $channel->getColor()->willReturn('black');
        $channelRepository->findOneByCode('en_US')->willReturn($channel);

        $this->__invoke('en_US')->shouldReturn('black');
    }
}
