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

namespace Tests\Sylius\InvoicingPlugin\Unit\EventProducer;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Sylius\InvoicingPlugin\EventProducer\OrderPaymentPaidProducer;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderPaymentPaidProducerTest extends TestCase
{
    #[Test]
    public function it_dispatches_order_payment_paid_event_for_payment(): void
    {
        $eventBus = $this->createMock(MessageBusInterface::class);
        $clock = $this->createMock(ClockInterface::class);
        $invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $payment = $this->createMock(PaymentInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $invoice = $this->createMock(InvoiceInterface::class);

        $dateTime = new \DateTimeImmutable();
        $event = new OrderPaymentPaid('0000001', $dateTime);

        $payment->method('getOrder')->willReturn($order);
        $order->method('getNumber')->willReturn('0000001');
        $clock->method('now')->willReturn($dateTime);
        $invoiceRepository->method('findOneByOrder')->with($order)->willReturn($invoice);

        $eventBus
            ->expects(self::once())
            ->method('dispatch')
            ->with($event)
            ->willReturn(new Envelope($event));

        $producer = new OrderPaymentPaidProducer($eventBus, $clock, $invoiceRepository);
        $producer($payment);
    }

    #[Test]
    public function it_does_not_dispatch_event_when_payment_is_not_related_to_order(): void
    {
        $eventBus = $this->createMock(MessageBusInterface::class);
        $clock = $this->createMock(ClockInterface::class);
        $invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $payment = $this->createMock(PaymentInterface::class);

        $payment->method('getOrder')->willReturn(null);

        $eventBus->expects($this->never())->method('dispatch');
        $clock->expects($this->never())->method('now');

        $producer = new OrderPaymentPaidProducer($eventBus, $clock, $invoiceRepository);
        $producer($payment);
    }

    #[Test]
    public function it_does_not_dispatch_event_when_there_is_no_invoice_related_to_order(): void
    {
        $eventBus = $this->createMock(MessageBusInterface::class);
        $clock = $this->createMock(ClockInterface::class);
        $invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $payment = $this->createMock(PaymentInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $payment->method('getOrder')->willReturn($order);
        $order->method('getNumber')->willReturn('0000001');
        $invoiceRepository->method('findOneByOrder')->with($order)->willReturn(null);

        $eventBus->expects($this->never())->method('dispatch');
        $clock->expects($this->never())->method('now');

        $producer = new OrderPaymentPaidProducer($eventBus, $clock, $invoiceRepository);
        $producer($payment);
    }
}
