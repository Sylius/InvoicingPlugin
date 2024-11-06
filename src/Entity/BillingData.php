<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

/** @final */
class BillingData implements BillingDataInterface, ResourceInterface
{
    protected int $id;

    public function __construct(
        protected string $firstName,
        protected string $lastName,
        protected string $countryCode,
        protected string $street,
        protected string $city,
        protected string $postcode,
        protected ?string $provinceCode = null,
        protected ?string $provinceName = null,
        protected ?string $company = null,
    ) {
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
