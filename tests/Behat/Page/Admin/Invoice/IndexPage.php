<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

final class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function hasInvoiceForOrder(string $orderNumber): bool
    {
        return $this->isSingleResourceOnPage(['orderNumber' => $orderNumber]);
    }

    public function hasInvoiceWithChannel(int $index, string $channel): bool
    {
        $invoice = $this->getDocument()->findAll('css', 'table tbody tr')[$index-1];

        return $invoice->find('css', sprintf('td:contains("%s")', $channel)) !== null;
    }

    public function filterByChannel(string $channelName): void
    {
        $this->getDocument()->find('css', '#criteria_channel_channel')->selectOption($channelName);
    }
}
