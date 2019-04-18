<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Twig;

use Sylius\InvoicingPlugin\Provider\ChannelColorProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class ChannelColorExtension extends AbstractExtension
{
    /** @var ChannelColorProviderInterface */
    private $channelColorProvider;

    public function __construct(ChannelColorProviderInterface $channelColorProvider)
    {
        $this->channelColorProvider = $channelColorProvider;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('sylius_channel_color', [$this->channelColorProvider, '__invoke']),
        ];
    }
}
