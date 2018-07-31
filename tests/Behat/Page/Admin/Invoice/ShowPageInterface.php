<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice;

use Sylius\Behat\Page\SymfonyPageInterface;

interface ShowPageInterface extends SymfonyPageInterface
{
    public function getIssuedAtDate(): \DateTimeInterface;

    public function hasBillingData(
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        string $countryName
    ): bool;

    public function hasShopBillingData(
        string $company,
        string $taxId,
        string $countryName,
        string $street,
        string $city,
        string $postcode
    ): bool;

    public function countItems(): int;

    public function hasItemWithData(
        string $name,
        string $unitPrice,
        int $quantity,
        string $taxTotal,
        string $total
    ): bool;

    public function hasTaxItem(string $label, string $amount): bool;

    public function getNumber(): string;

    public function getSubtotal(): string;

    public function getTotal(): string;

    public function download(): void;
}
