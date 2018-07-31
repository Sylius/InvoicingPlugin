<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventListener;

use Prooph\ServiceBus\CommandBus;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class OrderPaymentPaidListener
{
    /** @var CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(OrderPaymentPaid $event)
    {
        $this->commandBus->dispatch(new SendInvoiceEmail($event->orderNumber()));
    }
}
