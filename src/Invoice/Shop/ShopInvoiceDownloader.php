<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Invoice\Shop;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\InvoicingPlugin\Checker\InvoiceCustomerRelationCheckerInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;

final class ShopInvoiceDownloader implements ShopInvoiceDownloaderInterface
{
    /** @var InvoicePdfFileGeneratorInterface */
    private $invoicePdfFileGenerator;

    /** @var InvoiceCustomerRelationCheckerInterface */
    private $invoiceCustomerRelationChecker;

    public function __construct(
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        InvoiceCustomerRelationCheckerInterface $invoiceCustomerRelationChecker
    ) {
        $this->invoicePdfFileGenerator = $invoicePdfFileGenerator;
        $this->invoiceCustomerRelationChecker = $invoiceCustomerRelationChecker;
    }

    public function download(string $invoiceId, CustomerInterface $customer): InvoicePdf
    {
        $this->invoiceCustomerRelationChecker->check($invoiceId, $customer);

        return $this->invoicePdfFileGenerator->generate($invoiceId);
    }
}
