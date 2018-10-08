<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Application;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\CustomerRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Exception\InvoiceNotAccessible;
use Sylius\InvoicingPlugin\Invoice\Shop\ShopInvoiceDownloaderInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class InvoiceContext implements Context
{
    /** @var ShopInvoiceDownloaderInterface */
    private $invoiceDownloader;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var InvoiceRepository */
    private $invoiceRepository;

    public function __construct(
        ShopInvoiceDownloaderInterface $invoiceDownloader,
        CustomerRepositoryInterface $customerRepository,
        InvoiceRepository $invoiceRepository
    ) {
        $this->invoiceDownloader = $invoiceDownloader;
        $this->customerRepository = $customerRepository;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @When the customer :customerEmail tries to download the invoice for the order :orderNumber
     */
    public function customerTriesToDownloadInvoiceForOrder(string $customerEmail, string $orderNumber): void
    {
        /** @var CustomerInterface $customer */
        $customer = $this->customerRepository->findOneBy(['email' => $customerEmail]);

        /** @var InvoiceInterface $invoice */
        $invoice = $this->invoiceRepository->getOneByOrderNumber($orderNumber);

        try {
            $this->invoiceDownloader->download($invoice->id(), $customer);
        } catch (InvoiceNotAccessible $exception) {
            return;
        }

        throw new \Exception("Downloading invoice should fail");
    }

    /**
     * @Then the invoice for the order :orderNumber should not be downloaded
     */
    public function invoiceForOrderShouldNotBeDownloaded(string $orderNumber): void
    {
        // skipped intentionally - not relevant as the condition was checked in previous step
    }
}
