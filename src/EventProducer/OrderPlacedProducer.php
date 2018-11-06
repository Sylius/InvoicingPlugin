<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventProducer;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Event\OrderPlaced;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderPlacedProducer
{
    /** @var MessageBusInterface */
    private $eventBus;

    /** @var DateTimeProvider */
    private $dateTimeProvider;

    public function __construct(MessageBusInterface $eventBus, DateTimeProvider $dateTimeProvider)
    {
        $this->eventBus = $eventBus;
        $this->dateTimeProvider = $dateTimeProvider;
    }

    public function postPersist(LifecycleEventArgs $event): void
    {
        $order = $event->getEntity();

        if (
            !$order instanceof OrderInterface ||
            $order->getCheckoutState() !== OrderCheckoutStates::STATE_COMPLETED
        ) {
            return;
        }

        $this->dispatchOrderPlacedEvent($order);
    }

    public function postUpdate(LifecycleEventArgs $event): void
    {
        $order = $event->getEntity();

        if (!$order instanceof OrderInterface) {
            return;
        }

        $entityManager = $event->getEntityManager();

        $unitOfWork = $entityManager->getUnitOfWork();
        $changeSet = $unitOfWork->getEntityChangeSet($order);

        if (
            !isset($changeSet['checkoutState']) ||
            $changeSet['checkoutState'][1] !== OrderCheckoutStates::STATE_COMPLETED
        ) {
            return;
        }

        $this->dispatchOrderPlacedEvent($order);
    }

    private function dispatchOrderPlacedEvent(OrderInterface $order): void
    {
        $this->eventBus->dispatch(
            new OrderPlaced($order->getNumber(), $this->dateTimeProvider->__invoke())
        );
    }
}
