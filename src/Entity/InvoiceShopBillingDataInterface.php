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

interface InvoiceShopBillingDataInterface
{
    public function getId();

    public function getTaxId(): ?string;

    public function getCompany(): ?string;

    public function getCountryCode(): ?string;

    public function getStreet(): ?string;

    public function getCity(): ?string;

    public function getPostcode(): ?string;

    public function getRepresentative(): ?string;

    public function setCompany(?string $company): void;

    public function setTaxId(?string $taxId): void;

    public function setCountryCode(?string $countryCode): void;

    public function setStreet(?string $street): void;

    public function setCity(?string $city): void;

    public function setPostcode(?string $postcode): void;

    public function setRepresentative(?string $representative): void;
}
