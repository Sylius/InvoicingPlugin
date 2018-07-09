<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Order\ShowPageInterface;
use Webmozart\Assert\Assert;

final class CustomerBrowsingInvoicesContext implements Context
{
    /** @var ShowPageInterface */
    private $orderShowPage;

    public function __construct(ShowPageInterface $orderShowPage)
    {
        $this->orderShowPage = $orderShowPage;
    }

    /**
     * @When I click on first invoice's download button
     */
    public function clickOnFirstInvoiceDownloadButton(): void
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
}
