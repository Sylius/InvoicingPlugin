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

interface BillingDataInterface
{
    public function id(): int;

    public function firstName(): string;

    public function lastName(): string;

    public function countryCode(): string;

    public function street(): string;

    public function city(): string;

    public function postcode(): string;

    public function provinceCode(): ?string;

    public function provinceName(): ?string;

    public function company(): ?string;
}
