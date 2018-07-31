<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\EventListener;

use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\CommandBus;
use Prophecy\Argument;
use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Sylius\InvoicingPlugin\EventListener\OrderPaymentPaidListener;
use Symfony\Component\Validator\Constraints\Date;

final class OrderPaymentPaidListenerSpec extends ObjectBehavior
{
    function let(CommandBus $commandBus): void
    {
        $this->beConstructedWith($commandBus);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(OrderPaymentPaidListener::class);
    }

    function it_dispatches_send_invoice_email_command(CommandBus $commandBus): void
    {
        $commandBus->dispatch(Argument::that(function (SendInvoiceEmail $command): bool {
            return $command->orderNumber() === '00000001';
        }))->shouldBeCalled();

        $this(new OrderPaymentPaid('00000001', new \DateTime()));
    }
}
