<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\EventProducer;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\UnitOfWork;
use Mockery;
use Mockery\MockInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Event\OrderPlaced;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderPlacedProducerSpec extends ObjectBehavior
{
    function let(MessageBusInterface $eventBus, DateTimeProvider $dateTimeProvider): void
    {
        $this->beConstructedWith($eventBus, $dateTimeProvider);
    }

    function it_dispatches_an_order_placed_event_for_persisted_order(
        MessageBusInterface $eventBus,
        DateTimeProvider $dateTimeProvider,
        OrderInterface $order,
        ObjectManager $objectManager
    ): void {
        $dateTime = new \DateTime('2018-12-14');
        $dateTimeProvider->__invoke()->willReturn($dateTime);

        $order->getNumber()->willReturn('000666');
        $order->getCheckoutState()->willReturn(OrderCheckoutStates::STATE_COMPLETED);

        $postPersistEvent = new LifecycleEventArgs($order->getWrappedObject(), $objectManager->getWrappedObject());
        $orderPlacedEvent = new OrderPlaced('000666', $dateTime);

        $eventBus->dispatch($orderPlacedEvent)->shouldBeCalled()->willReturn(new Envelope($orderPlacedEvent));

        $this->postPersist($postPersistEvent);
    }

    function it_dispatches_an_order_placed_event_for_updated_order(
        MessageBusInterface $eventBus,
        DateTimeProvider $dateTimeProvider,
        EntityManagerInterface $entityManager,
        OrderInterface $order
    ): void {
        $dateTime = new \DateTime('2018-12-14');
        $dateTimeProvider->__invoke()->willReturn($dateTime);

        /** @var UnitOfWork|MockInterface $unitOfWork */
        $unitOfWork = Mockery::mock(UnitOfWork::class);
        $unitOfWork->shouldReceive('getEntityChangeSet')->withArgs([$order->getWrappedObject()])->andReturn([
            'checkoutState' => [OrderCheckoutStates::STATE_CART, OrderCheckoutStates::STATE_COMPLETED],
        ]);

        $entityManager->getUnitOfWork()->willReturn($unitOfWork);

        $order->getNumber()->willReturn('000666');

        $postUpdateEvent = new LifecycleEventArgs($order->getWrappedObject(), $entityManager->getWrappedObject());
        $orderPlacedEvent = new OrderPlaced('000666', $dateTime);

        $eventBus->dispatch($orderPlacedEvent)->shouldBeCalled()->willReturn(new Envelope($orderPlacedEvent));

        $this->postUpdate($postUpdateEvent);
    }

    function it_does_nothing_after_persisting_if_event_entity_is_not_order(
        MessageBusInterface $eventBus,
        LifecycleEventArgs $event
    ): void {
        $event->getEntity()->willReturn('notAnOrder');

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->postPersist($event);
    }

    function it_does_nothing_after_update_if_event_entity_is_not_order(
        MessageBusInterface $eventBus,
        LifecycleEventArgs $event
    ): void {
        $event->getEntity()->willReturn('notAnOrder');

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->postUpdate($event);
    }

    function it_does_nothing_after_persisting_if_order_is_not_completed(
        MessageBusInterface $eventBus,
        LifecycleEventArgs $event,
        OrderInterface $order
    ): void {
        $event->getEntity()->willReturn($order);

        $order->getCheckoutState()->willReturn(OrderCheckoutStates::STATE_CART);

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();

        $this->postPersist($event);
    }

    function it_does_nothing_after_update_if_order_checkout_state_has_not_changed(
        MessageBusInterface $eventBus,
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
        MessageBusInterface $eventBus,
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
