<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;

final class GeneratingInvoiceContext implements Context
{
    /** @var ObjectManager */
    private $invoiceManager;

    /** @var InvoiceRepositoryInterface */
    private $invoiceRepository;

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
        $invoice = $this->invoiceRepository->findOneByOrderNumber($orderNumber);

        $this->invoiceManager->remove($invoice);
    }
}
