<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

interface ShopBillingDataInterface
{
    public function getTaxId(): ?string;

    public function setTaxId(?string $taxId): void;

    public function getCompany(): ?string;

    public function setCompany(?string $company): void;

    public function getCountryCode(): ?string;

    public function setCountryCode(?string $countryCode): void;

    public function getStreet(): ?string;

    public function setStreet(?string $street): void;

    public function getCity(): ?string;

    public function setCity(?string $city): void;

    public function getPostcode(): ?string;

    public function setPostcode(?string $postcode): void;
}
