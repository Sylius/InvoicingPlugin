<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Invoice\Admin;

use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;

final class AdminInvoiceDownloader implements AdminInvoiceDownloaderInterface
{
    /** @var InvoicePdfFileGeneratorInterface */
    private $invoicePdfFileGenerator;

    public function __construct(InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator)
    {
        $this->invoicePdfFileGenerator = $invoicePdfFileGenerator;
    }

    public function download(string $invoiceId): InvoicePdf
    {
        return $this->invoicePdfFileGenerator->generate($invoiceId);
    }
}
