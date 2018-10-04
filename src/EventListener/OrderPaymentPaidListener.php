<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventListener;

use Prooph\ServiceBus\CommandBus;
use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;

final class OrderPaymentPaidListener
{
    /** @var CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(OrderPaymentPaid $event): void
    {
        $this->commandBus->dispatch(new SendInvoiceEmail($event->orderNumber()));
    }
}
