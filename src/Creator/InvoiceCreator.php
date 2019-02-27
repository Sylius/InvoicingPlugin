<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Creator;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Exception\InvoiceAlreadyGenerated;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class InvoiceCreator implements InvoiceCreatorInterface
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var InvoiceGeneratorInterface */
    private $invoiceGenerator;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->orderRepository = $orderRepository;
        $this->invoiceGenerator = $invoiceGenerator;
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
    }
}
