<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

interface BillingDataInterface
{
    public function id(): string;

    public function firstName(): string;

    public function lastName(): string;

    public function countryCode(): string;

    public function street(): string;

    public function city(): string;

    public function postcode(): string;

    public function provinceCode(): ?string;

    public function provinceName(): ?string;

    public function company(): ?string;

    public function vatNumber(): ?string;

    public function siret(): ?string;

    public function code(): ?string;

    public function setFirstName(?string $firstName): BillingDataInterface;

    public function setLastName(?string $lastName): BillingDataInterface;

    public function setCountryCode(?string $countryCode): BillingDataInterface;

    public function setStreet(?string $street): BillingDataInterface;

    public function setCity(?string $city): BillingDataInterface;

    public function setPostcode(?string $postcode): BillingDataInterface;

    public function setProvinceName(?string $provinceName): BillingDataInterface;

    public function setCompany(?string $company): BillingDataInterface;

    public function setVatNumber(?string $vatNumber): BillingDataInterface;

    public function setSiret(?string $siret): BillingDataInterface;

    public function setCode(?string $code): BillingDataInterface;
}
