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

    public function isInvoiceIdFilterAvailable(): bool
    {
        return $this->hasElement('invoiceIdFilterType') && $this->hasElement('invoiceIdFilterValue');
    }

    public function isInvoiceNumberFilterAvailable(): bool
    {
        return $this->hasElement('invoiceNumberFilterType') && $this->hasElement('invoiceNumberFilterValue');
    }

    public function isOrderNumberFilterAvailable(): bool
    {
        return $this->hasElement('orderNumberFilterType') && $this->hasElement('orderNumberFilterValue');
    }

    public function isIssuedAtFilterAvailable(): bool
    {
        return
            $this->hasElement('issuedAtFilterFromDate') &&
            $this->hasElement('issuedAtFilterFromTime') &&
            $this->hasElement('issuedAtFilterToDate') &&
            $this->hasElement('issuedAtFilterToTime')
        ;
    }

    public function isChannelFilterAvailable(): bool
    {
        return $this->hasElement('channelFilter');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'channelFilter' => '#criteria_channel_channel',
            'invoiceIdFilterType' => '#criteria_id_type',
            'invoiceIdFilterValue' => '#criteria_id_value',
            'invoiceNumberFilterType' => '#criteria_number_type',
            'invoiceNumberFilterValue' => '#criteria_number_value',
            'orderNumberFilterType' => '#criteria_orderNumber_type',
            'orderNumberFilterValue' => '#criteria_orderNumber_value',
            'issuedAtFilterFromDate' => '#criteria_issuedAt_from_date',
            'issuedAtFilterFromTime' => '#criteria_issuedAt_from_time',
            'issuedAtFilterToDate' => '#criteria_issuedAt_to_date',
            'issuedAtFilterToTime' => '#criteria_issuedAt_to_time',
        ]);
    }
}
