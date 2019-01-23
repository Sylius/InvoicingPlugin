<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

/** @final */
class BillingData implements BillingDataInterface, ResourceInterface
{
    /** @var int */
    private $id;

    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string|null */
    private $company;

    /** @var string */
    private $countryCode;

    /** @var string|null */
    private $provinceCode;

    /** @var string|null */
    private $provinceName;

    /** @var string */
    private $street;

    /** @var string */
    private $city;

    /** @var string */
    private $postcode;

    public function __construct(
        string $firstName,
        string $lastName,
        string $countryCode,
        string $street,
        string $city,
        string $postcode,
        ?string $provinceCode = null,
        ?string $provinceName = null,
        ?string $company = null
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
    }

    public function getId(): int
    {
        return $this->id();
    }

    public function id(): int
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
}
