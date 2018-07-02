<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Core\Model\AddressInterface;

interface ShopBillingDataAwareInterface
{
    public function getShopName(): ?string;

    public function setShopName(?string $shopName): void;

    public function getTaxId(): ?string;

    public function setTaxId(?string $taxId): void;

    public function getBillingAddress(): ?AddressInterface;

    public function setBillingAddress(?AddressInterface $billingAddress): void;
}
