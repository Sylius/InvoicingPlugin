<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\EventProducer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\UnitOfWork;
use Mockery;
use Mockery\MockInterface;
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
        EntityManagerInterface $entityManager,
        OrderInterface $order,
        \DateTime $dateTime
    ): void {
        $dateTimeProvider->__invoke()->willReturn($dateTime);

        $event->getEntity()->willReturn($order);

        $event->getEntityManager()->willReturn($entityManager);

        /** @var UnitOfWork|MockInterface $unitOfWork */
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldReceive('getEntityChangeSet')->withArgs([$order->getWrappedObject()])->andReturn([
            'checkoutState' => [OrderCheckoutStates::STATE_CART, OrderCheckoutStates::STATE_COMPLETED],
        ]);

        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $order->getNumber()->willReturn('000666');

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

    function it_does_nothing_after_persisting_if_event_entity_is_not_order(
        EventBus $eventBus,
        LifecycleEventArgs $event
    ): void {
        $event->getEntity()->willReturn('notAnOrder');

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->postPersist($event);
    }

    function it_does_nothing_after_update_if_event_entity_is_not_order(
        EventBus $eventBus,
        LifecycleEventArgs $event
    ): void {
        $event->getEntity()->willReturn('notAnOrder');

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->postUpdate($event);
    }

    function it_does_nothing_after_persisting_if_order_is_not_completed(
        EventBus $eventBus,
        LifecycleEventArgs $event,
        OrderInterface $order
    ): void {
        $event->getEntity()->willReturn($order);

        $order->getCheckoutState()->willReturn(OrderCheckoutStates::STATE_CART);

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->postPersist($event);
    }

    function it_does_nothing_after_update_if_order_checkout_state_has_not_changed(
        EventBus $eventBus,
        LifecycleEventArgs $event,
        EntityManagerInterface $entityManager,
        OrderInterface $order
    ): void {
        $event->getEntity()->willReturn($order);

        $event->getEntityManager()->willReturn($entityManager);

        /** @var UnitOfWork|MockInterface $unitOfWork */
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldReceive('getEntityChangeSet')->withArgs([$order->getWrappedObject()])->andReturn([]);

        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->postUpdate($event);
    }

    function it_does_nothing_after_update_if_order_checkout_state_has_not_changed_to_completed(
        EventBus $eventBus,
        LifecycleEventArgs $event,
        EntityManagerInterface $entityManager,
        OrderInterface $order
    ): void {
        $event->getEntity()->willReturn($order);

        $event->getEntityManager()->willReturn($entityManager);

        /** @var UnitOfWork|MockInterface $unitOfWork */
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldReceive('getEntityChangeSet')->withArgs([$order->getWrappedObject()])->andReturn([
            'checkoutState' => [OrderCheckoutStates::STATE_CART, OrderCheckoutStates::STATE_ADDRESSED],
        ]);

        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->postUpdate($event);
    }
}
