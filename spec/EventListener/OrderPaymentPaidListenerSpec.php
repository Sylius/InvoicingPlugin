<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderPaymentPaidListenerSpec extends ObjectBehavior
{
    function let(MessageBusInterface $commandBus): void
    {
        $this->beConstructedWith($commandBus);
    }

    function it_dispatches_send_invoice_email_command(MessageBusInterface $commandBus): void
    {
        $commandBus->dispatch(Argument::that(function (SendInvoiceEmail $command): bool {
            return $command->orderNumber() === '00000001';
        }))->shouldBeCalled();

        $this(new OrderPaymentPaid('00000001', new \DateTime()));
    }
}
