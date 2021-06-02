<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderPaymentPaidListenerSpec extends ObjectBehavior
{
    public function let(MessageBusInterface $commandBus): void
    {
        $this->beConstructedWith($commandBus);
    }

    public function it_dispatches_send_invoice_email_command(MessageBusInterface $commandBus): void
    {
        $command = new SendInvoiceEmail('00000001');
        $commandBus->dispatch($command)->shouldBeCalled()->willReturn(new Envelope($command));

        $this(new OrderPaymentPaid('00000001', new \DateTime()));
    }
}
