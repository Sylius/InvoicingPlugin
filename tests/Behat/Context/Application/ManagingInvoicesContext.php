<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Application;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Webmozart\Assert\Assert;

final class ManagingInvoicesContext implements Context
{
    /** @var string */
    private $invoicesSavePath;

    /** @var InvoiceRepositoryInterface */
    private $invoiceRepository;

    public function __construct(string $invoicesSavePath, InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoicesSavePath = $invoicesSavePath;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @Then the invoice for order :order should be saved on the server
     */
    public function theInvoiceForOrderShouldBeSavedOnTheServer(OrderInterface $order): void
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $this->invoiceRepository->findOneByOrder($order);
        $filePath = $this->invoicesSavePath.'/'.str_replace('/', '_', $invoice->number()).'.pdf';

        Assert::true(file_exists($filePath));
    }
}
