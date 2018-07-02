<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

interface ShopBillingDataAwareInterface
{
    public function getTaxId(): ?string;

    public function setTaxId(?string $taxId): void;

    public function getBillingData(): ?ShopBillingDataInterface;

    public function setBillingData(?ShopBillingDataInterface $billingData): void;
}
