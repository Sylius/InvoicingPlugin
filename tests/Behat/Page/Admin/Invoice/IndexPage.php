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
}
