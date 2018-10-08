<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Ui\Action\Admin;

use Sylius\InvoicingPlugin\Invoice\Admin\AdminInvoiceDownloaderInterface;
use Sylius\InvoicingPlugin\ResponseBuilder\InvoiceFileResponseBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

final class DownloadInvoiceAction
{
    /** @var AdminInvoiceDownloaderInterface */
    private $invoiceDownloader;

    /** @var InvoiceFileResponseBuilderInterface */
    private $invoiceFileResponseBuilder;

    public function __construct(
        AdminInvoiceDownloaderInterface $invoiceDownloader,
        InvoiceFileResponseBuilderInterface $invoiceFileResponseBuilder
    ) {
        $this->invoiceDownloader = $invoiceDownloader;
        $this->invoiceFileResponseBuilder = $invoiceFileResponseBuilder;
    }

    public function __invoke(string $id): Response
    {
        $invoicePdfFile = $this->invoiceDownloader->download($id);

        return $this->invoiceFileResponseBuilder->build(Response::HTTP_OK, $invoicePdfFile);
    }
}
