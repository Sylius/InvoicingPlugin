<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\EventProducer;

use Doctrine\ORM\Event\LifecycleEventArgs;
use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\EventBus;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Event\OrderPlaced;

final class OrderPlacedProducerSpec extends ObjectBehavior
{
    function let(EventBus $eventBus, DateTimeProvider $dateTimeProvider): void
    {
        $this->beConstructedWith($eventBus, $dateTimeProvider);
    }

    function it_dispatches_an_order_placed_event_for_persited_order(
        EventBus $eventBus,
        DateTimeProvider $dateTimeProvider,
        LifecycleEventArgs $event,
        OrderInterface $order,
        \DateTime $dateTime
    ): void {
        $dateTimeProvider->__invoke()->willReturn($dateTime);

        $event->getEntity()->willReturn($order);

        $order->getNumber()->willReturn('000666');
        $order->getCheckoutState()->willReturn(OrderCheckoutStates::STATE_COMPLETED);

        $eventBus
            ->dispatch(Argument::that(function (OrderPlaced $event) use ($dateTime): bool {
                return
                    $event->orderNumber() === '000666' &&
                    $event->date() === $dateTime->getWrappedObject()
                ;
            }))
            ->shouldBeCalled()
        ;

        $this->postPersist($event);
    }

    function it_dispatches_an_order_placed_event_for_updated_order(
        EventBus $eventBus,
        DateTimeProvider $dateTimeProvider,
        LifecycleEventArgs $event,
        OrderInterface $order,
        \DateTime $dateTime
    ): void {
        $dateTimeProvider->__invoke()->willReturn($dateTime);

        $event->getEntity()->willReturn($order);

        $order->getNumber()->willReturn('000666');
        $order->getCheckoutState()->willReturn(OrderCheckoutStates::STATE_COMPLETED);

        $eventBus
            ->dispatch(Argument::that(function (OrderPlaced $event) use ($dateTime): bool {
                return
                    $event->orderNumber() === '000666' &&
                    $event->date() === $dateTime->getWrappedObject()
                ;
            }))
            ->shouldBeCalled()
        ;

        $this->postUpdate($event);
    }

    function it_does_nothing_if_event_entity_is_not_order(EventBus $eventBus, LifecycleEventArgs $event): void
    {
        $event->getEntity()->willReturn('notAnOrder', 'notAnOrder');

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->postPersist($event);
        $this->postUpdate($event);
    }

    function it_does_nothing_if_order_is_not_completed(
        EventBus $eventBus,
        LifecycleEventArgs $event,
        OrderInterface $order
    ): void {
        $event->getEntity()->willReturn($order, $order);

        $order->getCheckoutState()->willReturn(OrderCheckoutStates::STATE_CART, OrderCheckoutStates::STATE_ADDRESSED);

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->postPersist($event);
        $this->postUpdate($event);
    }
}
