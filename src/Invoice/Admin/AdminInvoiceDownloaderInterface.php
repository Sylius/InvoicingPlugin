<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Invoice\Admin;

use Sylius\InvoicingPlugin\Model\InvoicePdf;

interface AdminInvoiceDownloaderInterface
{
    public function download(string $invoiceId): InvoicePdf;
}
