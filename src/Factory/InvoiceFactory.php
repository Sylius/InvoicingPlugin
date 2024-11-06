<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Factory;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;
use Webmozart\Assert\Assert;

final class InvoiceFactory implements InvoiceFactoryInterface
{
    /**
     * @param class-string $className
     */
    public function __construct(
        private readonly string $className,
        private readonly FactoryInterface $invoiceShopBillingDataFactory,
    ) {
    }

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
        ?InvoiceShopBillingDataInterface $shopBillingData = null,
    ): InvoiceInterface {
        /** @var InvoiceInterface $invoice */
        $invoice = new $this->className(
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
            $shopBillingData ?? $this->invoiceShopBillingDataFactory->createNew(),
        );

        Assert::isInstanceOf($invoice, InvoiceInterface::class);

        return $invoice;
    }
}
