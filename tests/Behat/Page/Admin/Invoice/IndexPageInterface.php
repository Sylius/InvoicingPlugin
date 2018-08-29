<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    public function hasInvoiceForOrder(string $orderNumber): bool;

    public function hasInvoiceWithChannel(int $index, string $channel): bool;

    public function filterByChannel(string $channelName): void;
}
