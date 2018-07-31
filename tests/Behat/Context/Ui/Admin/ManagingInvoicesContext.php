<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;
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

    /** @var InvoiceRepository */
    private $invoiceRepository;

    public function __construct(
        IndexPageInterface $indexPage,
        ShowPageInterface $showPage,
        OrderShowPageInterface $orderShowPage,
        InvoiceRepository $invoiceRepository
    ) {
        $this->indexPage = $indexPage;
        $this->showPage = $showPage;
        $this->orderShowPage = $orderShowPage;
        $this->invoiceRepository = $invoiceRepository;
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

        $invoiceId = $this->invoiceRepository->getOneByOrderNumber($order->getNumber())->id();

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
     * @When I resend the first invoice
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
     * @Then it should have shop billing data as :company, :taxId, :street, :postCode :city, :countryName
     */
    public function itShouldHaveShopBillingDataAs(
        string $company,
        string $taxId,
        string $countryName,
        string $street,
        string $postcode,
        string $city
    ): void {
        Assert::true($this->showPage->hasShopBillingData($company, $taxId, $countryName, $street, $city, $postcode));
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
     * @Then it should have a tax item :label with amount :amount
     */
    public function itShouldHaveATaxItemWithAmount(string $label, string $amount): void
    {
        Assert::true($this->showPage->hasTaxItem($label, $amount));
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
     * @Then its subtotal should be :total
     */
    public function itsSubtotalShouldBe(string $subtotal): void
    {
        Assert::same($this->showPage->getSubtotal(), $subtotal);
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

    /**
     * @When I download the invoice
     */
    public function downloadInvoice(): void
    {
        $this->showPage->download();
    }

    /**
     * @Then it should have sequential number generated with :index and current month and year
     */
    public function shouldHaveSequentialNumberGeneratedWithIndexAndCurrentMonthAndYear(string $index): void
    {
        Assert::same(
            $this->showPage->getNumber(),
            (new \DateTime())->format('Y/m') . '/' . $index
        );
    }
}
