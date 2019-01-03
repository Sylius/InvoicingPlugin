<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

/** @final */
class InvoiceShopBillingData implements InvoiceShopBillingDataInterface
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

    public function __construct(
        string $company,
        string $taxId,
        string $countryCode,
        string $street,
        string $city,
        string $postcode
    ) {
        $this->company = $company;
        $this->taxId = $taxId;
        $this->countryCode = $countryCode;
        $this->street = $street;
        $this->city = $city;
        $this->postcode = $postcode;
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
}
