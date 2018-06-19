<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventListener;

use Prooph\ServiceBus\EventBus;
use Sylius\Component\Core\Model\OrderInterface;
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

    public function __invoke(OrderInterface $order): void
    {
        $this->eventBus->dispatch(new OrderPlaced($order->getNumber(), $this->dateTimeProvider->__invoke()));
    }
}
