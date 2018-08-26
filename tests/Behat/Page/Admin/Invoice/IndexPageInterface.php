<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    public function hasInvoiceForOrder(string $orderNumber): bool;

    public function hasInvoiceWithChannel(int $index, string $channel): bool;

    public function isInvoiceIdFilterAvailable(): bool;

    public function isInvoiceNumberFilterAvailable(): bool;

    public function isOrderNumberFilterAvailable(): bool;

    public function isIssuedAtFilterAvailable(): bool;

    public function isChannelFilterAvailable(): bool;
}
