<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

final class InvoiceShopBillingData implements InvoiceShopBillingDataInterface
{
    /** @var string|null */
    private $company;

    /** @var string|null */
    private $taxId;

    /** @var string|null */
    private $countryCode;

    /** @var string|null */
    private $street;

    /** @var string|null */
    private $city;

    /** @var string|null */
    private $postcode;

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    public function setTaxId(?string $taxId): void
    {
        $this->taxId = $taxId;
    }

    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function setPostcode(?string $postcode): void
    {
        $this->postcode = $postcode;
    }
}
