<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Order\DownloadInvoicePageInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Order\ShowPageInterface;
use Webmozart\Assert\Assert;

final class CustomerBrowsingInvoicesContext implements Context
{
    private ShowPageInterface $orderShowPage;

    private DownloadInvoicePageInterface $downloadInvoicePage;

    private InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(
        ShowPageInterface $orderShowPage,
        DownloadInvoicePageInterface $downloadInvoicePage,
        InvoiceRepositoryInterface $invoiceRepository
    ) {
        $this->orderShowPage = $orderShowPage;
        $this->downloadInvoicePage = $downloadInvoicePage;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @When I download the first invoice
     */
    public function downloadFirstInvoice(): void
    {
        $this->orderShowPage->downloadFirstInvoice();
    }

    /**
     * @Then the pdf file for this invoice should be downloaded successfully
     */
    public function pdfFileForThisInvoiceShouldBeDownloadedSuccessfully(): void
    {
        Assert::true($this->orderShowPage->isPdfFileDownloaded());
    }

    /**
     * @When I try to download the invoice for the order :orderNumber
     */
    public function tryToDownloadInvoiceForOrder(string $orderNumber): void
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $this->invoiceRepository->findByOrderNumber($orderNumber)[0];

        $this->downloadInvoicePage->tryToOpen(['id' => $invoice->id()]);
    }

    /**
     * @Then the invoice for the order :orderNumber should not be downloaded
     */
    public function invoiceForOrderShouldNotBeDownloaded(string $orderNumber): void
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $this->invoiceRepository->findByOrderNumber($orderNumber)[0];

        Assert::false($this->downloadInvoicePage->isOpen(['id' => $invoice->id()]));
    }

    /**
     * @Then I should not be able to download the first invoice
     */
    public function iShouldNotBeAbleToDownloadTheFirstInvoice(): void
    {
        Assert::false($this->orderShowPage->hasDownloadButtonForInvoice());
    }
}
