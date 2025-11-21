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

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\InvoicingPlugin\Event\OrderPlaced;
use Sylius\InvoicingPlugin\EventProducer\OrderPlacedProducer;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderPlacedProducerTest extends TestCase
{
    #[Test]
    public function it_dispatches_an_order_placed_event_for_persisted_order(): void
    {
        $eventBus = $this->createMock(MessageBusInterface::class);
        $clock = $this->createMock(ClockInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);

        $dateTime = new \DateTimeImmutable('2018-12-14');
        $clock->method('now')->willReturn($dateTime);

        $order->method('getNumber')->willReturn('000666');
        $order->method('getCheckoutState')->willReturn(OrderCheckoutStates::STATE_COMPLETED);

        $postPersistEvent = new LifecycleEventArgs($order, $entityManager);
        $orderPlacedEvent = new OrderPlaced('000666', $dateTime);

        $eventBus
            ->expects(self::once())
            ->method('dispatch')
            ->with($orderPlacedEvent)
            ->willReturn(new Envelope($orderPlacedEvent));

        $producer = new OrderPlacedProducer($eventBus, $clock);
        $producer->postPersist($postPersistEvent);
    }

    #[Test]
    public function it_dispatches_an_order_placed_event_for_updated_order(): void
    {
        $eventBus = $this->createMock(MessageBusInterface::class);
        $clock = $this->createMock(ClockInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $unitOfWork = $this->createMock(UnitOfWork::class);

        $dateTime = new \DateTimeImmutable('2018-12-14');
        $clock->method('now')->willReturn($dateTime);

        $unitOfWork
            ->method('getEntityChangeSet')
            ->with($order)
            ->willReturn([
                'checkoutState' => [OrderCheckoutStates::STATE_CART, OrderCheckoutStates::STATE_COMPLETED],
            ]);

        $entityManager->method('getUnitOfWork')->willReturn($unitOfWork);
        $order->method('getNumber')->willReturn('000666');

        $postUpdateEvent = new LifecycleEventArgs($order, $entityManager);
        $orderPlacedEvent = new OrderPlaced('000666', $dateTime);

        $eventBus
            ->expects(self::once())
            ->method('dispatch')
            ->with($orderPlacedEvent)
            ->willReturn(new Envelope($orderPlacedEvent));

        $producer = new OrderPlacedProducer($eventBus, $clock);
        $producer->postUpdate($postUpdateEvent);
    }

    #[Test]
    public function it_does_nothing_after_persisting_if_event_entity_is_not_order(): void
    {
        $eventBus = $this->createMock(MessageBusInterface::class);
        $clock = $this->createMock(ClockInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $notAnOrder = new \stdClass();
        $event = new LifecycleEventArgs($notAnOrder, $entityManager);

        $eventBus->expects($this->never())->method('dispatch');

        $producer = new OrderPlacedProducer($eventBus, $clock);
        $producer->postPersist($event);
    }

    #[Test]
    public function it_does_nothing_after_update_if_event_entity_is_not_order(): void
    {
        $eventBus = $this->createMock(MessageBusInterface::class);
        $clock = $this->createMock(ClockInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $notAnOrder = new \stdClass();
        $event = new LifecycleEventArgs($notAnOrder, $entityManager);

        $eventBus->expects($this->never())->method('dispatch');

        $producer = new OrderPlacedProducer($eventBus, $clock);
        $producer->postUpdate($event);
    }

    #[Test]
    public function it_does_nothing_after_persisting_if_order_is_not_completed(): void
    {
        $eventBus = $this->createMock(MessageBusInterface::class);
        $clock = $this->createMock(ClockInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $event = new LifecycleEventArgs($order, $entityManager);

        $order->method('getCheckoutState')->willReturn(OrderCheckoutStates::STATE_CART);

        $eventBus->expects($this->never())->method('dispatch');

        $producer = new OrderPlacedProducer($eventBus, $clock);
        $producer->postPersist($event);
    }

    #[Test]
    public function it_does_nothing_after_update_if_order_checkout_state_has_not_changed(): void
    {
        $eventBus = $this->createMock(MessageBusInterface::class);
        $clock = $this->createMock(ClockInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $unitOfWork = $this->createMock(UnitOfWork::class);
        $event = new LifecycleEventArgs($order, $entityManager);

        $unitOfWork->method('getEntityChangeSet')->with($order)->willReturn([]);
        $entityManager->method('getUnitOfWork')->willReturn($unitOfWork);

        $eventBus->expects($this->never())->method('dispatch');

        $producer = new OrderPlacedProducer($eventBus, $clock);
        $producer->postUpdate($event);
    }

    #[Test]
    public function it_does_nothing_after_update_if_order_checkout_state_has_not_changed_to_completed(): void
    {
        $eventBus = $this->createMock(MessageBusInterface::class);
        $clock = $this->createMock(ClockInterface::class);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $unitOfWork = $this->createMock(UnitOfWork::class);
        $event = new LifecycleEventArgs($order, $entityManager);

        $unitOfWork
            ->method('getEntityChangeSet')
            ->with($order)
            ->willReturn([
                'checkoutState' => [OrderCheckoutStates::STATE_CART, OrderCheckoutStates::STATE_ADDRESSED],
            ]);

        $entityManager->method('getUnitOfWork')->willReturn($unitOfWork);

        $eventBus->expects($this->never())->method('dispatch');

        $producer = new OrderPlacedProducer($eventBus, $clock);
        $producer->postUpdate($event);
    }
}
