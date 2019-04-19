<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Provider;

interface ChannelColorProviderInterface
{
    public function provide(string $channelCode): string;
}
