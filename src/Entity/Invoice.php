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

namespace Sylius\InvoicingPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;

/** @final */
class Invoice implements InvoiceInterface
{
    protected string $id;

    protected string $number;

    protected OrderInterface $order;

    protected \DateTimeInterface $issuedAt;

    protected BillingDataInterface $billingData;

    protected string $currencyCode;

    protected string $localeCode;

    protected int $total;

    /** @var Collection|LineItemInterface[] */
    protected Collection $lineItems;

    /** @var Collection|TaxItemInterface[] */
    protected Collection $taxItems;

    protected ChannelInterface $channel;

    protected string $paymentState;

    protected InvoiceShopBillingDataInterface $shopBillingData;

    public function __construct(
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
        InvoiceShopBillingDataInterface $shopBillingData
    ) {
        $this->id = $id;
        $this->number = $number;
        $this->order = $order;
        $this->issuedAt = clone $issuedAt;
        $this->billingData = $billingData;
        $this->currencyCode = $currencyCode;
        $this->localeCode = $localeCode;
        $this->total = $total;
        $this->lineItems = $lineItems;
        $this->taxItems = $taxItems;
        $this->channel = $channel;
        $this->paymentState = $paymentState;
        $this->shopBillingData = $shopBillingData;

        /** @var LineItemInterface $lineItem */
        foreach ($lineItems as $lineItem) {
            $lineItem->setInvoice($this);
        }

        /** @var TaxItemInterface $taxItem */
        foreach ($taxItems as $taxItem) {
            $taxItem->setInvoice($this);
        }
    }

    public function getId(): string
    {
        return $this->id();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function number(): string
    {
        return $this->number;
    }

    public function order(): OrderInterface
    {
        return $this->order;
    }

    public function orderNumber(): string
    {
        return (string) $this->order->getNumber();
    }

    public function issuedAt(): \DateTimeInterface
    {
        return clone $this->issuedAt;
    }

    public function billingData(): BillingDataInterface
    {
        return $this->billingData;
    }

    public function currencyCode(): string
    {
        return $this->currencyCode;
    }

    public function localeCode(): string
    {
        return $this->localeCode;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function lineItems(): Collection
    {
        return $this->lineItems;
    }

    public function taxItems(): Collection
    {
        return $this->taxItems;
    }

    public function subtotal(): int
    {
        $subtotal = 0;

        /** @var LineItemInterface $lineItem */
        foreach ($this->lineItems as $lineItem) {
            $subtotal += $lineItem->subtotal();
        }

        return $subtotal;
    }

    public function taxesTotal(): int
    {
        $taxesTotal = 0;

        /** @var LineItemInterface $lineItem */
        foreach ($this->lineItems as $lineItem) {
            $taxesTotal += $lineItem->taxTotal();
        }

        return $taxesTotal;
    }

    public function channel(): ChannelInterface
    {
        return $this->channel;
    }

    public function shopBillingData(): InvoiceShopBillingDataInterface
    {
        return $this->shopBillingData;
    }

    public function paymentState(): string
    {
        return $this->paymentState;
    }
}
