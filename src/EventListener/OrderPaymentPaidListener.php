<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventListener;

use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class OrderPaymentPaidListener
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function __invoke(OrderPaymentPaid $event)
    {
        $invoice = $this->invoiceRepository->findOneByOrderNumber($event->orderNumber());


    }
}
