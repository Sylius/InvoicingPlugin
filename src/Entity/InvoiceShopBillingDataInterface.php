<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

interface InvoiceShopBillingDataInterface
{
    public function getTaxId(): ?string;

    public function getCompany(): ?string;

    public function getCountryCode(): ?string;

    public function getStreet(): ?string;

    public function getCity(): ?string;

    public function getPostcode(): ?string;
}
