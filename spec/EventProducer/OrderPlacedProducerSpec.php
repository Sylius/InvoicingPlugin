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

namespace spec\Sylius\InvoicingPlugin\EventProducer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\UnitOfWork;
use Doctrine\Persistence\ObjectManager;
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

        $orderPlacedEvent = new OrderPlaced('000666', $dateTime);

        $eventBus->dispatch($orderPlacedEvent)->shouldBeCalled()->willReturn(new Envelope($orderPlacedEvent));

        $this->__invoke($order);
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

        $orderPlacedEvent = new OrderPlaced('000666', $dateTime);

        $eventBus->dispatch($orderPlacedEvent)->shouldBeCalled()->willReturn(new Envelope($orderPlacedEvent));

        $this->__invoke($order);
    }

    function it_does_nothing_after_persisting_if_event_entity_is_not_order(
        MessageBusInterface $eventBus,
        LifecycleEventArgs $event
    ): void {
        $event->getEntity()->willReturn('notAnOrder');

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();
    }

    function it_does_nothing_after_update_if_event_entity_is_not_order(
        MessageBusInterface $eventBus,
        LifecycleEventArgs $event
    ): void {
        $event->getEntity()->willReturn('notAnOrder');

        $eventBus->dispatch(Argument::any())->shouldNotBeCalled();
    }
}
