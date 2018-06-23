<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Doctrine\Common\Collections\Collection;

interface InvoiceInterface
{
    public function id(): string;

    public function orderNumber(): string;

    public function issuedAt(): \DateTimeInterface;

    public function billingData(): BillingDataInterface;

    public function currencyCode(): string;

    public function taxTotal(): int;

    public function total(): int;

    public function lineItems(): Collection;
}
