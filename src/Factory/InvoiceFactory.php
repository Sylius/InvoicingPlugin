<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Factory;

use Doctrine\Common\Collections\Collection;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Sylius\InvoicingPlugin\Entity\Invoice;
use Sylius\InvoicingPlugin\Entity\InvoiceChannelInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingData;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;

final class InvoiceFactory implements InvoiceFactoryInterface
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
        InvoiceChannelInterface $channel,
        InvoiceShopBillingDataInterface $shopBillingData = null
    ): InvoiceInterface {
        return new Invoice(
            $id,
            $number,
            $orderNumber,
            $issuedAt,
            $billingData,
            $currencyCode,
            $localeCode,
            $total,
            $lineItems,
            $taxItems,
            $channel,
            $shopBillingData ?? new InvoiceShopBillingData()
        );
    }
}
