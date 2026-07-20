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

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Creator\InvoiceCreatorInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceFileNameGeneratorInterface;
use Sylius\InvoicingPlugin\Provider\InvoiceFileProviderInterface;
use Symfony\Component\Clock\ClockInterface;
use Webmozart\Assert\Assert;

final class InvoiceNumberContext implements Context
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly InvoiceCreatorInterface $invoiceCreator,
        private readonly InvoiceFileProviderInterface $invoiceFileProvider,
        private readonly InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator,
        private readonly ManagerRegistry $managerRegistry,
        private readonly ClockInterface $clock,
        private readonly string $invoicesSavePath,
    ) {
    }

    /**
     * @Then the invoice for order :order should have number :number
     */
    public function theInvoiceForOrderShouldHaveNumber(OrderInterface $order, string $number): void
    {
        Assert::same($this->getInvoiceForOrder($order)->number(), $number);
    }

    /**
     * @Then the order :order should have no invoice
     */
    public function theOrderShouldHaveNoInvoice(OrderInterface $order): void
    {
        Assert::null($this->invoiceRepository->findOneByOrder($order));
    }

    /**
     * @Then it should not be possible to generate an invoice for order :order
     */
    public function itShouldNotBePossibleToGenerateAnInvoiceForOrder(OrderInterface $order): void
    {
        $orderNumber = (string) $order->getNumber();

        try {
            ($this->invoiceCreator)($orderNumber, $this->clock->now());
        } catch (\Throwable) {
            $this->managerRegistry->resetManager();

            return;
        }

        throw new \DomainException(sprintf(
            'Generating an invoice for order %s was expected to fail, but it succeeded',
            $orderNumber,
        ));
    }

    /**
     * @Given the invoice file for order :order has been removed from the server
     */
    public function theInvoiceFileForOrderHasBeenRemovedFromTheServer(OrderInterface $order): void
    {
        $filePath = $this->getInvoiceFilePath($this->getInvoiceForOrder($order));

        Assert::true(file_exists($filePath), sprintf('Expected the invoice file "%s" to exist', $filePath));

        unlink($filePath);
    }

    /**
     * @Then the invoice for order :order should be downloadable with number :number
     */
    public function theInvoiceForOrderShouldBeDownloadableWithNumber(OrderInterface $order, string $number): void
    {
        $invoice = $this->getInvoiceForOrder($order);
        Assert::same($invoice->number(), $number);

        $invoicePdf = $this->invoiceFileProvider->provide($invoice);

        Assert::same($invoicePdf->filename(), $this->invoiceFileNameGenerator->generateForPdf($invoice));
        Assert::notEmpty($invoicePdf->content());
        Assert::true(file_exists($this->getInvoiceFilePath($invoice)));
    }

    private function getInvoiceForOrder(OrderInterface $order): InvoiceInterface
    {
        $invoice = $this->invoiceRepository->findOneByOrder($order);

        Assert::isInstanceOf($invoice, InvoiceInterface::class, sprintf(
            'No invoice has been generated for order %s',
            $order->getNumber(),
        ));

        return $invoice;
    }

    private function getInvoiceFilePath(InvoiceInterface $invoice): string
    {
        return rtrim($this->invoicesSavePath, '/') . '/' . $this->invoiceFileNameGenerator->generateForPdf($invoice);
    }
}
