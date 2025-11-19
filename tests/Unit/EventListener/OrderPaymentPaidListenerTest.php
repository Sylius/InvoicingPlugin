<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Unit\EventListener;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Sylius\InvoicingPlugin\EventListener\OrderPaymentPaidListener;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderPaymentPaidListenerTest extends TestCase
{
    #[Test]
    public function it_dispatches_send_invoice_email_command(): void
    {
        $commandBus = $this->createMock(MessageBusInterface::class);
        $command = new SendInvoiceEmail('00000001');

        $commandBus
            ->expects(self::once())
            ->method('dispatch')
            ->with($command)
            ->willReturn(new Envelope($command));

        $listener = new OrderPaymentPaidListener($commandBus);
        $listener(new OrderPaymentPaid('00000001', new \DateTime()));
    }
}
