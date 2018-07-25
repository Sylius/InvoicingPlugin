<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventListener;

use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Event\OrderPlaced;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class CreateInvoiceOnOrderPlacedListener
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

    public function __invoke(OrderPlaced $event): void
    {
        $order = $this->orderRepository->findOneBy(['number' => $event->orderNumber()]);
        $invoice = $this->invoiceGenerator->generateForOrder($order, $event->date());

        $this->invoiceRepository->add($invoice);
    }
}
