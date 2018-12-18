<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Channel;

use Sylius\Behat\Page\Admin\Channel\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function specifyCompany(string $company): void;

    public function hasCompany(string $company): bool;

    public function specifyTaxId(string $taxId): void;

    public function hasTaxId(string $taxId): bool;

    public function specifyBillingAddress(string $street, string $postcode, string $city, string $countryCode): void;

    public function hasBillingAddress(string $street, string $postcode, string $city, string $countryCode): bool;

    public function hasValidationError(string $message): bool;
}
