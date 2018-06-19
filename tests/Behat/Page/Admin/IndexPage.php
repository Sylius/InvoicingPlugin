<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

final class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function hasInvoiceForOrder(string $orderNumber): bool
    {
        return $this->isSingleResourceOnPage(['orderNumber' => $orderNumber]);
    }

    public function getInvoiceIdForOrder(string $orderNumber): string
    {
        return $this->getTableAccessor()->getFieldFromRow(
            $this->getElement('table'),
            $this->getTableAccessor()->getRowWithFields($this->getElement('table'), ['orderNumber' => $orderNumber]),
            'id'
        )->getText();
    }
}
