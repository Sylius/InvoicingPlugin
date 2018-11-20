<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Order;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

final class DownloadInvoicePage extends SymfonyPage implements DownloadInvoicePageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_invoicing_plugin_shop_invoice_download';
    }
}
