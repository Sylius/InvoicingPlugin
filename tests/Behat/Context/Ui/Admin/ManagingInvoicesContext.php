<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\OrderInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\IndexPageInterface;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\ShowPageInterface;
use Webmozart\Assert\Assert;

final class ManagingInvoicesContext implements Context
{
    /** @var IndexPageInterface */
    private $indexPage;

    /** @var ShowPageInterface */
    private $showPage;

    public function __construct(IndexPageInterface $indexPage, ShowPageInterface $showPage)
    {
        $this->indexPage = $indexPage;
        $this->showPage = $showPage;
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
}
