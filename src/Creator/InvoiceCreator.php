<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Creator;

use Doctrine\ORM\ORMException;
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
    private InvoiceRepositoryInterface $invoiceRepository;

    private OrderRepositoryInterface $orderRepository;

    private InvoiceGeneratorInterface $invoiceGenerator;

    private InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator;

    private InvoiceFileManagerInterface $invoiceFileManager;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        InvoiceFileManagerInterface $invoiceFileManager
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->orderRepository = $orderRepository;
        $this->invoiceGenerator = $invoiceGenerator;
        $this->invoicePdfFileGenerator = $invoicePdfFileGenerator;
        $this->invoiceFileManager = $invoiceFileManager;
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
        $invoicePdf = $this->invoicePdfFileGenerator->generate($invoice);
        $this->invoiceFileManager->save($invoicePdf);

        try {
            $this->invoiceRepository->add($invoice);
        } catch (ORMException $exception) {
            $this->invoiceFileManager->remove($invoicePdf);
        }
    }
}
