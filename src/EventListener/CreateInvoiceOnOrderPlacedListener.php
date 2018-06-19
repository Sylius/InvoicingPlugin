<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventListener;

use Sylius\InvoicingPlugin\Entity\Invoice;
use Sylius\InvoicingPlugin\Event\OrderPlaced;
use Sylius\InvoicingPlugin\InvoiceIdentifierGenerator;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class CreateInvoiceOnOrderPlacedListener
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var InvoiceIdentifierGenerator */
    private $invoiceIdentifierGenerator;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        InvoiceIdentifierGenerator $invoiceIdentifierGenerator
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceIdentifierGenerator = $invoiceIdentifierGenerator;
    }

    public function __invoke(OrderPlaced $event): void
    {
        $invoice = new Invoice(
            $this->invoiceIdentifierGenerator->__invoke($event->orderId()),
            $event->orderId(),
            $event->date()
        );

        $this->invoiceRepository->add($invoice);
    }
}
