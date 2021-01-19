<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

class BillingData implements BillingDataInterface, ResourceInterface
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $firstName;

    /** @var string */
    protected $lastName;

    /** @var string|null */
    protected $company;

    /** @var string */
    protected $countryCode;

    /** @var string|null */
    protected $provinceCode;

    /** @var string|null */
    protected $provinceName;

    /** @var string */
    protected $street;

    /** @var string */
    protected $city;

    /** @var string */
    protected $postcode;

    /** @var string|null */
    protected $vatNumber;

    public function __construct(
        string $firstName,
        string $lastName,
        string $countryCode,
        string $street,
        string $city,
        string $postcode,
        ?string $provinceCode = null,
        ?string $provinceName = null,
        ?string $company = null,
        ?string $vatNumber = null
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->countryCode = $countryCode;
        $this->street = $street;
        $this->city = $city;
        $this->postcode = $postcode;
        $this->provinceCode = $provinceCode;
        $this->provinceName = $provinceName;
        $this->company = $company;
        $this->vatNumber = $vatNumber;
    }

    public function getId(): string
    {
        return $this->id();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function countryCode(): string
    {
        return $this->countryCode;
    }

    public function street(): string
    {
        return $this->street;
    }

    public function city(): string
    {
        return $this->city;
    }

    public function postcode(): string
    {
        return $this->postcode;
    }

    public function provinceCode(): ?string
    {
        return $this->provinceCode;
    }

    public function provinceName(): ?string
    {
        return $this->provinceName;
    }

    public function company(): ?string
    {
        return $this->company;
    }

    public function vatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function setFirstName(?string $firstName): BillingDataInterface
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function setLastName(?string $lastName): BillingDataInterface
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function setCountryCode(?string $countryCode): BillingDataInterface
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    public function setStreet(?string $street): BillingDataInterface
    {
        $this->street = $street;
        return $this;
    }

    public function setCity(?string $city): BillingDataInterface
    {
        $this->city = $city;
        return $this;
    }

    public function setPostcode(?string $postcode): BillingDataInterface
    {
        $this->postcode = $postcode;
        return $this;
    }

    public function setProvinceName(?string $provinceName): BillingDataInterface
    {
        $this->provinceName = $provinceName;
        return $this;
    }

    public function setCompany(?string $company): BillingDataInterface
    {
        $this->company = $company;
        return $this;
    }

    public function setVatNumber(?string $vatNumber): BillingDataInterface
    {
        $this->vatNumber = $vatNumber;
        return $this;
    }
}
