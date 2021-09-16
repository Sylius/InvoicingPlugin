<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

/** @final */
class InvoiceShopBillingData implements InvoiceShopBillingDataInterface, ResourceInterface
{
    /** @var mixed */
    protected $id;

    protected ?string $company = null;

    protected ?string $taxId = null;

    protected ?string $countryCode = null;

    protected ?string $street = null;

    protected ?string $city = null;

    protected ?string $postcode = null;

    protected ?string $representative = null;

    public function getId()
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
