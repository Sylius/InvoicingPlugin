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

namespace Sylius\InvoicingPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;

/** @final */
class Invoice implements InvoiceInterface
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $number;

    /** @var OrderInterface */
    protected $order;

    /** @var \DateTimeInterface */
    protected $issuedAt;

    /** @var BillingDataInterface */
    protected $billingData;

    /** @var string */
    protected $currencyCode;

    /** @var string */
    protected $localeCode;

    /** @var int */
    protected $total;

    /** @var Collection|LineItemInterface[] */
    protected $lineItems;

    /** @var Collection|TaxItemInterface[] */
    protected $taxItems;

    /** @var ChannelInterface */
    protected $channel;

    /** @var InvoiceShopBillingDataInterface */
    protected $shopBillingData;

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

    /** @deprecated this function is deprecated and will be removed in v1.0 - Use Invoice::getId() instead */
    public function id(): string
    {
        return $this->id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    /** @deprecated this function is deprecated and will be removed in v1.0 Use Invoice::getNumber() instead */
    public function number(): string
    {
        return $this->number;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    /** @deprecated this function is deprecated and will be removed in v1.0 Use Invoice::getOrder() instead */
    public function order(): OrderInterface
    {
        return $this->order;
    }

    public function getOrder(): OrderInterface
    {
        return $this->order;
    }

    public function setOrder(OrderInterface $order): void
    {
        $this->order = $order;
    }

    /** @deprecated this function is deprecated and will be removed in v1.0 Use Invoice::getOrder()->getNumber() instead */
    public function orderNumber(): string
    {
        return (string) $this->order->getNumber();
    }

    /** @deprecated this function is deprecated and will be removed in v1.0 Use Invoice::getIssuedAt() instead */
    public function issuedAt(): \DateTimeInterface
    {
        return clone $this->issuedAt;
    }

    public function getIssuedAt(): \DateTimeInterface
    {
        return $this->issuedAt;
    }

    public function setIssuedAt(\DateTimeInterface $issuedAt): void
    {
        $this->issuedAt = $issuedAt;
    }

    /** @deprecated this function is deprecated and will be removed in v1.0 Use Invoice::getBillingData() instead */
    public function billingData(): BillingDataInterface
    {
        return $this->billingData;
    }

    public function getBillingData(): BillingDataInterface
    {
        return $this->billingData;
    }

    public function setBillingData(BillingDataInterface $billingData): void
    {
        $this->billingData = $billingData;
    }

    /** @deprecated this function is deprecated and will be removed in v1.0 Use Invoice::getCurrencyCode() instead */
    public function currencyCode(): string
    {
        return $this->currencyCode;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    /** @deprecated this function is deprecated and will be removed in v1.0 Use Invoice::getLocaleCode() instead */
    public function localeCode(): string
    {
        return $this->localeCode;
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function setLocaleCode(string $localeCode): void
    {
        $this->localeCode = $localeCode;
    }

    /** @deprecated this function is deprecated and will be removed in v1.0 Use Invoice::getTotal() instead */
    public function total(): int
    {
        return $this->total;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    /** @deprecated this function is deprecated and will be removed in v1.0 Use Invoice::getLineItems() instead */
    public function lineItems(): Collection
    {
        return $this->lineItems;
    }

    public function getLineItems(): Collection
    {
        return $this->lineItems;
    }

    public function setLineItems(ArrayCollection $lineItems): void
    {
        $this->lineItems = $lineItems;
    }

    /** @deprecated this function is deprecated and will be removed in v1.0 Use Invoice::getTaxItems() instead */
    public function taxItems(): Collection
    {
        return $this->taxItems;
    }

    public function getTaxItems(): Collection
    {
        return $this->taxItems;
    }

    public function setTaxItems(ArrayCollection $taxItems): void
    {
        $this->taxItems = $taxItems;
    }

    /** @deprecated this function is deprecated and will be removed in v1.0 Use Invoice::getSubtotal() instead */
    public function subtotal(): int
    {
        $subtotal = 0;

        /** @var LineItemInterface $lineItem */
        foreach ($this->lineItems as $lineItem) {
            $subtotal += $lineItem->subtotal();
        }

        return $subtotal;
    }

    /** @deprecated this function is deprecated and will be removed in v1.0 Use Invoice::getTaxesTotal() instead */
    public function taxesTotal(): int
    {
        return $this->getTaxesTotal();
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

    /** @deprecated this function is deprecated and will be removed in v1.0 Use Invoice::getChannel() instead */
    public function channel(): ChannelInterface
    {
        return $this->channel;
    }

    public function getChannel(): ChannelInterface
    {
        return $this->channel;
    }

    public function setChannel(ChannelInterface $channel): void
    {
        $this->channel = $channel;
    }

    /** @deprecated this function is deprecated and will be removed in v1.0 Use Invoice::getShopBillingData() instead */
    public function shopBillingData(): InvoiceShopBillingDataInterface
    {
        return $this->shopBillingData;
    }

    public function getShopBillingData(): InvoiceShopBillingDataInterface
    {
        return $this->shopBillingData;
    }

    public function setShopBillingData(InvoiceShopBillingDataInterface $shopBillingData): void
    {
        $this->shopBillingData = $shopBillingData;
    }
}
