<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Provider;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelColorProvider implements ChannelColorProviderInterface
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var string */
    private $defaultChannelColor;

    public function __construct(ChannelRepositoryInterface $channelRepository, string $defaultChannelColor)
    {
        $this->channelRepository = $channelRepository;
        $this->defaultChannelColor = $defaultChannelColor;
    }

    public function provide(string $channelCode): string
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);

        return $channel->getColor() ?? $this->defaultChannelColor;
    }
}
