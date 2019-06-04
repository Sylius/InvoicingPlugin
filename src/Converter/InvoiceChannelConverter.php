<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Converter;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceChannel;
use Sylius\InvoicingPlugin\Entity\InvoiceChannelInterface;

final class InvoiceChannelConverter implements InvoiceChannelConverterInterface
{
    public function convert(ChannelInterface $channel): InvoiceChannelInterface
    {
        return new InvoiceChannel($channel->getCode(), $channel->getName());
    }
}
