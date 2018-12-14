<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Twig;

use Sylius\Component\Core\Model\ChannelInterface;

interface FindChannelByInvoiceChannelCodeExtensionInterface
{
    public function getFilters(): array;
    public function findByInvoiceChannelCode(string $invoiceChannelCode): ChannelInterface;
}
