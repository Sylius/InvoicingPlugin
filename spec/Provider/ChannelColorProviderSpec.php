<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\InvoicingPlugin\Provider\ChannelColorProviderInterface;

final class ChannelColorProviderSpec extends ObjectBehavior
{
    function let(ChannelRepositoryInterface $channelRepository): void
    {
        $this->beConstructedWith($channelRepository, 'whiteGrey');
    }

    function it_implements_channel_color_provider_interface(): void
    {
        $this->shouldImplement(ChannelColorProviderInterface::class);
    }

    function it_returns_channel_color(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
    ): void {
        $channel->getColor()->willReturn('black');
        $channelRepository->findOneByCode('en_US')->willReturn($channel);

        $this->provide('en_US')->shouldReturn('black');
    }

    function it_returns_default_channel_color_if_channel_does_not_provide_one(
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
    ): void {
        $channel->getColor()->willReturn(null);
        $channelRepository->findOneByCode('en_US')->willReturn($channel);

        $this->provide('en_US')->shouldReturn('whiteGrey');
    }
}
