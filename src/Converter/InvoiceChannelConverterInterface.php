<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Converter;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceChannelInterface;

interface InvoiceChannelConverterInterface
{
    public function convert(ChannelInterface $channel): InvoiceChannelInterface;
}
