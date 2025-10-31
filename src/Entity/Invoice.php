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
    public function __construct(
        protected string $id,
        protected string $number,
        protected OrderInterface $order,
        protected \DateTimeInterface $issuedAt,
        protected BillingDataInterface $billingData,
        protected string $currencyCode,
        protected string $localeCode,
        protected int $total,
        /** @var Collection|LineItemInterface[] */
        protected Collection $lineItems,
        /** @var Collection|TaxItemInterface[] */
        protected Collection $taxItems,
        protected ChannelInterface $channel,
        protected string $paymentState,
        protected InvoiceShopBillingDataInterface $shopBillingData,
    ) {
        $this->issuedAt = clone $issuedAt;

        /** @var LineItemInterface $lineItem */
        foreach ($this->lineItems as $lineItem) {
            $lineItem->setInvoice($this);
        }

        /** @var TaxItemInterface $taxItem */
        foreach ($this->taxItems as $taxItem) {
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

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }

    public function setOrder(OrderInterface $order): void
    {
        $this->order = $order;
    }

    public function getIssuedAt(): \DateTimeInterface
    {
        return clone $this->issuedAt;
    }

    public function getBillingData(): BillingDataInterface
    {
        return $this->billingData;
    }

    public function setBillingData(BillingDataInterface $billingData): void
    {
        $this->billingData = $billingData;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function setLocaleCode(string $localeCode): void
    {
        $this->localeCode = $localeCode;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function getLineItems(): Collection
    {
        return $this->lineItems;
    }

    public function addLineItem(LineItemInterface $lineItem): void
    {
        if (!$this->lineItems->contains($lineItem)) {
            $this->lineItems->add($lineItem);
            $lineItem->setInvoice($this);
        }
    }

    public function getTaxItems(): Collection
    {
        return $this->taxItems;
    }

    public function addTaxItem(TaxItemInterface $taxItem): void
    {
        if (!$this->taxItems->contains($taxItem)) {
            $this->taxItems->add($taxItem);
            $taxItem->setInvoice($this);
        }
    }

    public function getSubtotal(): int
    {
        $subtotal = 0;

        /** @var LineItemInterface $lineItem */
        foreach ($this->lineItems as $lineItem) {
            $subtotal += $lineItem->subtotal();
        }

        return $subtotal;
    }

    public function getTaxesTotal(): int
    {
        $taxesTotal = 0;

        /** @var LineItemInterface $lineItem */
        foreach ($this->lineItems as $lineItem) {
            $taxesTotal += $lineItem->taxTotal();
        }

        return $taxesTotal;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    public function getShopBillingData(): InvoiceShopBillingDataInterface
    {
        return $this->shopBillingData;
    }

    public function setShopBillingData(InvoiceShopBillingDataInterface $shopBillingData): void
    {
        $this->shopBillingData = $shopBillingData;
    }

    public function getPaymentState(): string
    {
        return $this->paymentState;
    }

    public function setPaymentState(string $paymentState): void
    {
        $this->paymentState = $paymentState;
    }
}
