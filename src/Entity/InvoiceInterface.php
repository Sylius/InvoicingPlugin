<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;

interface InvoiceInterface
{
    public function id(): string;

    public function number(): string;

    public function orderNumber(): string;

    public function issuedAt(): \DateTimeInterface;

    public function billingData(): BillingDataInterface;

    public function currencyCode(): string;

    public function localeCode(): string;

    public function total(): int;

    /** @return Collection<array-key, LineItemInterface> */
    public function lineItems(): Collection;

    /** @return Collection<array-key, TaxItemInterface> */
    public function taxItems(): Collection;

    public function subtotal(): int;

    public function channel(): ChannelInterface;

    public function shopBillingData(): InvoiceShopBillingDataInterface;
}
