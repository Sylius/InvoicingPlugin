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
use Sylius\Component\Resource\Model\ResourceInterface;

interface InvoiceInterface extends ResourceInterface
{
    public const PAYMENT_STATE_COMPLETED = 'completed';

    public const PAYMENT_STATE_PENDING = 'pending';

    public function getId(): string;

    public function getNumber(): string;

    public function setNumber(string $number): void;

    public function getOrder(): OrderInterface;

    public function setOrder(OrderInterface $order): void;

    public function getIssuedAt(): \DateTimeInterface;

    public function getBillingData(): BillingDataInterface;

    public function setBillingData(BillingDataInterface $billingData): void;

    public function getCurrencyCode(): string;

    public function setCurrencyCode(string $currencyCode): void;

    public function getLocaleCode(): string;

    public function setLocaleCode(string $localeCode): void;

    public function getTotal(): int;

    public function setTotal(int $total): void;

    public function getLineItems(): Collection;

    public function addLineItem(LineItemInterface $lineItem): void;

    public function getTaxItems(): Collection;

    public function addTaxItem(TaxItemInterface $taxItem): void;

    public function getSubtotal(): int;

    public function getTaxesTotal(): int;

    public function getChannel(): ChannelInterface;

    public function setChannel(ChannelInterface $channel): void;

    public function getShopBillingData(): InvoiceShopBillingDataInterface;

    public function setShopBillingData(InvoiceShopBillingDataInterface $shopBillingData): void;

    public function getPaymentState(): string;

    public function setPaymentState(string $paymentState): void;
}
