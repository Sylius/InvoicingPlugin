<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Provider;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\InvoicingPlugin\Provider\ChannelColorProviderInterface;

final class ChannelColorProvider implements ChannelColorProviderInterface
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    public function __invoke(string $channelCode): string
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($channelCode);

        return $channel->getColor();
    }
}
