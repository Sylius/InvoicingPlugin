<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\EventProducer;

use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\EventBus;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;

final class OrderPaymentPaidProducerSpec extends ObjectBehavior
{
    function let(EventBus $eventBus, DateTimeProvider $dateTimeProvider): void
    {
        $this->beConstructedWith($eventBus, $dateTimeProvider);
    }

    function it_dispatches_order_payment_paid_event_for_payment(
        EventBus $eventBus,
        DateTimeProvider $dateTimeProvider,
        PaymentInterface $payment,
        OrderInterface $order
    ): void {
        $payment->getOrder()->willReturn($order);

        $order->getNumber()->shouldBeCalled();

        $dateTimeProvider->__invoke()->willReturn(new \DateTime('now'));

        $eventBus->dispatch(Argument::type(OrderPaymentPaid::class))->shouldBeCalled();

        $this->__invoke($payment);
    }

    function it_does_not_dispatch_event_when_payment_is_not_related_to_order(
        EventBus $eventBus,
        DateTimeProvider $dateTimeProvider,
        PaymentInterface $payment
    ): void {
        $payment->getOrder()->willReturn(null);

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $dateTimeProvider->__invoke()->shouldNotBeCalled();

        $this->__invoke($payment);
    }
}
