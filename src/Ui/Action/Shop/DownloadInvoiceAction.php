<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Ui\Action\Shop;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\InvoicingPlugin\Exception\InvoiceNotAccessible;
use Sylius\InvoicingPlugin\Invoice\Shop\ShopInvoiceDownloaderInterface;
use Sylius\InvoicingPlugin\ResponseBuilder\InvoiceFileResponseBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

final class DownloadInvoiceAction
{
    /** @var ShopInvoiceDownloaderInterface */
    private $invoiceDownloader;

    /** @var InvoiceFileResponseBuilderInterface */
    private $invoiceFileResponseBuilder;

    /** @var CustomerContextInterface */
    private $customerContext;

    public function __construct(
        ShopInvoiceDownloaderInterface $invoiceDownloader,
        InvoiceFileResponseBuilderInterface $invoiceFileResponseBuilder,
        CustomerContextInterface $customerContext
    ) {
        $this->invoiceDownloader = $invoiceDownloader;
        $this->invoiceFileResponseBuilder = $invoiceFileResponseBuilder;
        $this->customerContext = $customerContext;
    }

    public function __invoke(string $id): Response
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerContext->getCustomer();

        try {
            $invoicePdfFile = $this->invoiceDownloader->download($id, $customer);

            return $this->invoiceFileResponseBuilder->build(Response::HTTP_OK, $invoicePdfFile);
        } catch (InvoiceNotAccessible $exception) {
            return new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }
}
