<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Factory;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;

interface InvoiceFactoryInterface
{
    public function createForData(
        string $id,
        string $number,
        string $orderNumber,
        \DateTimeInterface $issuedAt,
        BillingDataInterface $billingData,
        string $currencyCode,
        string $localeCode,
        int $total,
        Collection $lineItems,
        Collection $taxItems,
        ChannelInterface $channel,
        InvoiceShopBillingDataInterface $shopBillingData = null
    ): InvoiceInterface;
}
