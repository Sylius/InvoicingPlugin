<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\EventProducer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderPaymentPaidProducerSpec extends ObjectBehavior
{
    function let(
        MessageBusInterface $eventBus,
        DateTimeProvider $dateTimeProvider,
        InvoiceRepository $invoiceRepository
    ): void {
        $this->beConstructedWith($eventBus, $dateTimeProvider, $invoiceRepository);
    }

    function it_dispatches_order_payment_paid_event_for_payment(
        MessageBusInterface $eventBus,
        DateTimeProvider $dateTimeProvider,
        PaymentInterface $payment,
        OrderInterface $order,
        InvoiceRepository $invoiceRepository,
        InvoiceInterface $invoice
    ): void {
        $payment->getOrder()->willReturn($order);
        $order->getNumber()->willReturn('0000001');

        $dateTime = new \DateTime();
        $dateTimeProvider->__invoke()->willReturn($dateTime);

        $event = new OrderPaymentPaid('0000001', $dateTime);

        $invoiceRepository->findOneByOrderNumber('0000001')->willReturn($invoice);

        $eventBus->dispatch($event)->shouldBeCalled()->willReturn(new Envelope($event));

        $this->__invoke($payment);
    }

    function it_does_not_dispatch_event_when_payment_is_not_related_to_order(
        MessageBusInterface $eventBus,
        DateTimeProvider $dateTimeProvider,
        PaymentInterface $payment
    ): void {
        $payment->getOrder()->willReturn(null);

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $dateTimeProvider->__invoke()->shouldNotBeCalled();

        $this->__invoke($payment);
    }

    function it_does_not_dispatch_event_when_there_is_no_invoice_related_to_order(
        MessageBusInterface $eventBus,
        DateTimeProvider $dateTimeProvider,
        PaymentInterface $payment,
        OrderInterface $order,
        InvoiceRepository $invoiceRepository
    ): void {
        $payment->getOrder()->willReturn($order);
        $order->getNumber()->willReturn('0000001');
        $invoiceRepository->findOneByOrderNumber('0000001')->willReturn(null);

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();
        $dateTimeProvider->__invoke()->shouldNotBeCalled();

        $this->__invoke($payment);
    }
}
