<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

/** @final */
class ShopBillingData implements ShopBillingDataInterface
{
    /** @var string */
    protected $id;

    /** @var string|null */
    protected $company;

    /** @var string|null */
    protected $taxId;

    /** @var string|null */
    protected $countryCode;

    /** @var string|null */
    protected $street;

    /** @var string|null */
    protected $city;

    /** @var string|null */
    protected $postcode;

    public function getId(): string
    {
        return $this->id;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): void
    {
        $this->company = $company;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function setTaxId(?string $taxId): void
    {
        $this->taxId = $taxId;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(?string $street): void
    {
        $this->street = $street;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(?string $postcode): void
    {
        $this->postcode = $postcode;
    }
}
