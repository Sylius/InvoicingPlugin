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

namespace Tests\Sylius\InvoicingPlugin\Unit\Provider;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\InvoicingPlugin\Provider\ChannelColorProvider;
use Sylius\InvoicingPlugin\Provider\ChannelColorProviderInterface;

final class ChannelColorProviderTest extends TestCase
{
    private ChannelRepositoryInterface&MockObject $channelRepository;

    private ChannelColorProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);

        $this->provider = new ChannelColorProvider($this->channelRepository, 'whiteGrey');
    }

    #[Test]
    public function it_implements_channel_color_provider_interface(): void
    {
        self::assertInstanceOf(ChannelColorProviderInterface::class, $this->provider);
    }

    #[Test]
    public function it_returns_channel_color(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $channel->method('getColor')->willReturn('black');
        $this->channelRepository->method('findOneByCode')->with('en_US')->willReturn($channel);

        $result = $this->provider->provide('en_US');

        self::assertSame('black', $result);
    }

    #[Test]
    public function it_returns_default_channel_color_if_channel_does_not_provide_one(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $channel->method('getColor')->willReturn(null);
        $this->channelRepository->method('findOneByCode')->with('en_US')->willReturn($channel);

        $result = $this->provider->provide('en_US');

        self::assertSame('whiteGrey', $result);
    }
}
