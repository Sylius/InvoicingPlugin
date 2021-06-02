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
            new TwigFilter('sylius_channel_color', [$this->channelColorProvider, 'provide']),
        ];
    }
}
