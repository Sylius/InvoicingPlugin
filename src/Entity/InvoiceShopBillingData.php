<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

/** @final */
class InvoiceShopBillingData implements InvoiceShopBillingDataInterface, ResourceInterface
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

    /** @var string|null */
    protected $representative;

    public function getId(): string
    {
        return $this->id;
    }

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

    public function getRepresentative(): ?string
    {
        return $this->representative;
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

    public function setRepresentative(?string $representative): void
    {
        $this->representative = $representative;
    }
}
