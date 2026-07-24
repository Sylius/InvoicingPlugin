<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Application;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Webmozart\Assert\Assert;

final class ManagingInvoicesContext implements Context
{
    private string $invoicesSavePath;

    private InvoiceRepositoryInterface $invoiceRepository;

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
        $filePath = $this->invoicesSavePath . '/' . str_replace('/', '_', $invoice->number()) . '.pdf';

        Assert::true(file_exists($filePath));
    }
}
