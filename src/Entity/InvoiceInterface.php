<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Doctrine\Common\Collections\Collection;

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

    public function lineItems(): Collection;

    public function taxItems(): Collection;

    public function subtotal(): int;

    public function channel(): InvoiceChannelInterface;

    public function shopBillingData(): InvoiceShopBillingDataInterface;
}
