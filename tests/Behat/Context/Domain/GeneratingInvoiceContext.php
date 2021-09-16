<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;

final class GeneratingInvoiceContext implements Context
{
    private ObjectManager $invoiceManager;

    private InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(ObjectManager $invoiceManager, InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceManager = $invoiceManager;
        $this->invoiceRepository = $invoiceRepository;
    }

    /**
     * @Given the order :orderNumber has lost all of its invoices
     */
    public function orderHasLostAllOfItsInvoices(string $orderNumber): void
    {
        $invoice = $this->invoiceRepository->findByOrderNumber($orderNumber)[0];

        $this->invoiceManager->remove($invoice);
    }
}
