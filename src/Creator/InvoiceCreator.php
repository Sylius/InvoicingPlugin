<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Creator;

use League\Flysystem\FilesystemInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Exception\InvoiceAlreadyGenerated;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class InvoiceCreator implements InvoiceCreatorInterface
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var InvoiceGeneratorInterface */
    private $invoiceGenerator;

    /** @var InvoicePdfFileGeneratorInterface */
    private $invoicePdfFileGenerator;

    /** @var FilesystemInterface */
    private $invoiceFilesystem;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        FilesystemInterface $invoiceFilesystem
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->orderRepository = $orderRepository;
        $this->invoiceGenerator = $invoiceGenerator;
        $this->invoicePdfFileGenerator = $invoicePdfFileGenerator;
        $this->invoiceFilesystem = $invoiceFilesystem;
    }

    public function __invoke(string $orderNumber, \DateTimeInterface $dateTime): void
    {
        /** @var InvoiceInterface|null $invoice */
        $invoice = $this->invoiceRepository->findOneByOrderNumber($orderNumber);

        if (null !== $invoice) {
            throw InvoiceAlreadyGenerated::withOrderNumber($orderNumber);
        }

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        $invoice = $this->invoiceGenerator->generateForOrder($order, $dateTime);

        $this->invoiceRepository->add($invoice);

        $pdfInvoice = $this->invoicePdfFileGenerator->generate($invoice);

        $this->invoiceFilesystem->put(
            $pdfInvoice->filename(),
            $pdfInvoice->content()
        );
    }
}
