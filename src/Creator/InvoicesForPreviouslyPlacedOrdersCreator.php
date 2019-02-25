<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Creator;

use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class InvoiceForPreviouslyPlacedOrdersCreator implements InvoiceForPreviouslyPlacedOrdersCreatorInterface
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var InvoiceGeneratorInterface */
    private $invoiceGenerator;

    /** @var DateTimeProvider */
    private $dateTimeProvider;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator,
        DateTimeProvider $dateTimeProvider
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->orderRepository = $orderRepository;
        $this->invoiceGenerator = $invoiceGenerator;
        $this->dateTimeProvider = $dateTimeProvider;
    }

    public function __invoke(array $orders): void
    {
        /** @var OrderInterface $order */
        foreach ($orders as $order) {

            /** @var InvoiceInterface|null $invoice */
            $invoice = $this->invoiceRepository->findOneByOrderNumber($order->getNumber());

            if (null !== $invoice) {
                continue;
            }

            $invoice = $this->invoiceGenerator->generateForOrder($order, $this->dateTimeProvider->__invoke());

            $this->invoiceRepository->add($invoice);
        }
    }
}
