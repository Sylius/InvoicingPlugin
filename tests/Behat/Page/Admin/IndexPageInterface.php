<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    public function hasInvoiceForOrder(string $orderNumber): bool;

    public function getInvoiceIdForOrder(string $orderNumber): string;
}
