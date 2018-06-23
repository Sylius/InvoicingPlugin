<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventListener;

use Sylius\InvoicingPlugin\Event\OrderPlaced;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class CreateInvoiceOnOrderPlacedListener
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var InvoiceGeneratorInterface */
    private $invoiceGenerator;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        InvoiceGeneratorInterface $invoiceGenerator
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceGenerator = $invoiceGenerator;
    }

    public function __invoke(OrderPlaced $event): void
    {
        $invoice = $this->invoiceGenerator->generateForOrder($event->order(), $event->date());

        $this->invoiceRepository->add($invoice);
    }
}
