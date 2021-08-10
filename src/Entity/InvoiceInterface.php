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
use Sylius\Component\Resource\Model\ResourceInterface;

interface InvoiceInterface extends ResourceInterface
{
    /** @deprecated this function is deprecated and will be removed in v1.0 - Use InvoiceInterface::getId() instead */
    public function id(): string;

    public function setId($id): void;

    public function getNumber(): string;

    public function setNumber(string $number): void;

    public function getOrder(): OrderInterface;

    public function setOrder(OrderInterface $order): void;

    public function getIssuedAt(): \DateTimeInterface;

    public function setIssuedAt(\DateTimeInterface $issuedAt): void;

    public function getBillingData(): BillingDataInterface;

    public function setBillingData(BillingDataInterface $billingData): void;

    public function getCurrencyCode(): string;

    public function setCurrencyCode(string $currencyCode): void;

    public function getLocaleCode(): string;

    public function setLocaleCode(string $localeCode): void;

    public function getTotal(): int;

    public function setTotal(int $total): void;

    public function getLineItems(): Collection;

    public function setLineItems(ArrayCollection $lineItems): void;

    public function getTaxItems(): Collection;

    public function setTaxItems(ArrayCollection $taxItems): void;

    public function getTaxesTotal(): int;

    public function getChannel(): ChannelInterface;

    public function setChannel(ChannelInterface $channel): void;

    public function getShopBillingData(): InvoiceShopBillingDataInterface;

    public function setShopBillingData(InvoiceShopBillingDataInterface $shopBillingData): void;

    public function number(): string;

    public function order(): OrderInterface;

    /** @deprecated this method is deprecated and will be removed in v1.0 - Use InvoiceInterface::order() instead */
    public function orderNumber(): string;

    public function issuedAt(): \DateTimeInterface;

    public function billingData(): BillingDataInterface;

    public function currencyCode(): string;

    public function localeCode(): string;

    public function total(): int;

    public function lineItems(): Collection;

    public function taxItems(): Collection;

    public function subtotal(): int;

    public function taxesTotal(): int;

    public function channel(): ChannelInterface;

    public function shopBillingData(): InvoiceShopBillingDataInterface;
}
