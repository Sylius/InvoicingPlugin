<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\OrderInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice\IndexPageInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Invoice\ShowPageInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Order\ShowPageInterface as OrderShowPageInterface;
use Webmozart\Assert\Assert;

final class ManagingInvoicesContext implements Context
{
    /** @var IndexPageInterface */
    private $indexPage;

    /** @var ShowPageInterface */
    private $showPage;

    /** @var OrderShowPageInterface */
    private $orderShowPage;

    public function __construct(
        IndexPageInterface $indexPage,
        ShowPageInterface $showPage,
        OrderShowPageInterface $orderShowPage
    ) {
        $this->indexPage = $indexPage;
        $this->showPage = $showPage;
        $this->orderShowPage = $orderShowPage;
    }

    /**
     * @When I browse invoices
     */
    public function browseInvoices(): void
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should see a single invoice for order :order
     */
    public function shouldSeeSingleInvoiceForOrder(OrderInterface $order): void
    {
        Assert::true($this->indexPage->hasInvoiceForOrder($order->getNumber()));
    }

    /**
     * @When I view the summary of the invoice for order :order
     * @Then I should see the summary of the invoice for order :order
     */
    public function viewSummaryOfInvoiceForOrder(OrderInterface $order): void
    {
        $this->indexPage->open();

        $invoiceId = $this->indexPage->getInvoiceIdForOrder($order->getNumber());

        $this->showPage->open(['id' => $invoiceId]);
    }

    /**
     * @Then it should be issued in the last hour
     */
    public function shouldBeIssuedInTheLastHour(): void
    {
        Assert::true(
            ((new \DateTimeImmutable('now'))->getTimestamp() - $this->showPage->getIssuedAtDate()->getTimestamp()) <= 3600
        );
    }

    /**
     * @Then I should see an invoice related to this order
     */
    public function shouldSeeAnInvoiceRelatedToTheOrder(): void
    {
        Assert::same(1, $this->orderShowPage->countRelatedInvoices());
    }

    /**
     * @When I click on first invoice's id
     */
    public function clickOnFirstInvoiceId(): void
    {
        $this->orderShowPage->clickOnFirstInvoiceId();
    }

    /**
     * @When I click on first invoice's download button
     */
    public function clickOnFirstInvoiceDownloadButton(): void
    {
        $this->orderShowPage->downloadFirstInvoice();
    }

    /**
     * @When I click on first invoice's resend button
     */
    public function clickOnFirstInvoiceResendButton(): void
    {
        $this->orderShowPage->resendFirstInvoice();
    }

    /**
     * @Then it should have billing data as :customerName, :street, :postcode :city, :countryName
     */
    public function itShouldHaveBillingDataAs(
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        string $countryName
    ): void {
        Assert::true($this->showPage->hasBillingData($customerName, $street, $postcode, $city, $countryName));
    }

    /**
     * @Then it should have :amountOfItems items in the list
     */
    public function itShouldHaveItemsInTheList(int $amountOfItems): void
    {
        Assert::same($this->showPage->countItems(), $amountOfItems);
    }

    /**
     * @Then it should have an item :name with unit price :unitPrice, quantity :quantity, tax total :taxTotal and total :total
     */
    public function itShouldHaveAnItemWithData(
        string $name,
        string $unitPrice,
        int $quantity,
        string $taxTotal,
        string $total
    ): void {
        Assert::true($this->showPage->hasItemWithData(sprintf('%s (%s)', $name, $name), $unitPrice, $quantity, $taxTotal, $total));
    }

    /**
     * @Then it should have a shipping item :name with unit price :unitPrice, quantity :quantity, tax total :taxTotal and total :total
     */
    public function itShouldHaveAShippingItemWithData(
        string $name,
        string $unitPrice,
        int $quantity,
        string $taxTotal,
        string $total
    ): void {
        Assert::true($this->showPage->hasItemWithData($name, $unitPrice, $quantity, $taxTotal, $total));
    }

    /**
     * @Then its tax total should be :taxTotal
     */
    public function itsTaxTotalShouldBe(string $taxTotal): void
    {
        Assert::same($this->showPage->getTaxTotal(), $taxTotal);
    }

    /**
     * @Then its total should be :total
     */
    public function itsTotalShouldBe(string $total): void
    {
        Assert::same($this->showPage->getTotal(), $total);
    }

    /**
     * @Then the pdf file for this invoice should be downloaded successfully
     */
    public function pdfFileForThisInvoiceShouldBeDownloadedSuccessfully(): void
    {
        Assert::true($this->orderShowPage->isPdfFileDownloaded());
    }
}
