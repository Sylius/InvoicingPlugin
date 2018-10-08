<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Invoice\Shop;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;

interface ShopInvoiceDownloaderInterface
{
    public function download(string $invoiceId, CustomerInterface $customer): InvoicePdf;
}
