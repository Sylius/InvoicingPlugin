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

namespace Sylius\InvoicingPlugin\Factory;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Sylius\InvoicingPlugin\Entity\Invoice;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingData;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;

final class InvoiceFactory implements InvoiceFactoryInterface
{
    public function createForData(
        string $id,
        string $number,
        OrderInterface $order,
        \DateTimeInterface $issuedAt,
        BillingDataInterface $billingData,
        string $currencyCode,
        string $localeCode,
        int $total,
        Collection $lineItems,
        Collection $taxItems,
        ChannelInterface $channel,
        string $paymentState,
        InvoiceShopBillingDataInterface $shopBillingData = null
    ): InvoiceInterface {
        return new Invoice(
            $id,
            $number,
            $order,
            $issuedAt,
            $billingData,
            $currencyCode,
            $localeCode,
            $total,
            $lineItems,
            $taxItems,
            $channel,
            $paymentState,
            $shopBillingData ?? new InvoiceShopBillingData()
        );
    }
}
