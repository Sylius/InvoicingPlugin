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

namespace Sylius\InvoicingPlugin\Creator;

use Doctrine\ORM\Exception\ORMException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Exception\InvoiceAlreadyGenerated;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Manager\InvoiceFileManagerInterface;

final class InvoiceCreator implements InvoiceCreatorInterface
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly InvoiceGeneratorInterface $invoiceGenerator,
        private readonly InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        private readonly InvoiceFileManagerInterface $invoiceFileManager,
        private readonly bool $hasEnabledPdfFileGenerator = true,
    ) {
    }

    public function __invoke(string $orderNumber, \DateTimeInterface $dateTime): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        /** @var InvoiceInterface|null $invoice */
        $invoice = $this->invoiceRepository->findOneByOrder($order);

        if (null !== $invoice) {
            throw InvoiceAlreadyGenerated::withOrderNumber($orderNumber);
        }

        $invoice = $this->invoiceGenerator->generateForOrder($order, $dateTime);

        if (!$this->hasEnabledPdfFileGenerator) {
            $this->invoiceRepository->add($invoice);

            return;
        }

        $invoicePdf = $this->invoicePdfFileGenerator->generate($invoice);
        $this->invoiceFileManager->save($invoicePdf);

        try {
            $this->invoiceRepository->add($invoice);
        } catch (ORMException) {
            $this->invoiceFileManager->remove($invoicePdf);
        }
    }
}
