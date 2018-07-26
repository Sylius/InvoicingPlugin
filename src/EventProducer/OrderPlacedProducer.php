<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventProducer;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Prooph\ServiceBus\EventBus;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Event\OrderPlaced;

final class OrderPlacedProducer
{
    /** @var EventBus */
    private $eventBus;

    /** @var DateTimeProvider */
    private $dateTimeProvider;

    public function __construct(EventBus $eventBus, DateTimeProvider $dateTimeProvider)
    {
        $this->eventBus = $eventBus;
        $this->dateTimeProvider = $dateTimeProvider;
    }

    public function postPersist(LifecycleEventArgs $event): void
    {
        $this->dispatchOrderPlacedEventIfNecessary($event);
    }

    public function postUpdate(LifecycleEventArgs $event): void
    {
        $this->dispatchOrderPlacedEventIfNecessary($event);
    }

    private function dispatchOrderPlacedEventIfNecessary(LifecycleEventArgs $event): void
    {
        $order = $event->getEntity();

        if (
            !$order instanceof OrderInterface ||
            $order->getCheckoutState() !== OrderCheckoutStates::STATE_COMPLETED
        ) {
            return;
        }

        $this->eventBus->dispatch(
            new OrderPlaced($order->getNumber(), $this->dateTimeProvider->__invoke())
        );
    }
}
