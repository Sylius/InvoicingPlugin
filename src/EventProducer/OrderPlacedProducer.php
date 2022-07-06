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

namespace Sylius\InvoicingPlugin\EventProducer;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Event\OrderPlaced;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderPlacedProducer
{
    private MessageBusInterface $eventBus;

    private DateTimeProvider $dateTimeProvider;

    public function __construct(MessageBusInterface $eventBus, DateTimeProvider $dateTimeProvider)
    {
        $this->eventBus = $eventBus;
        $this->dateTimeProvider = $dateTimeProvider;
    }

    public function __invoke(OrderInterface $order): void
    {
        $this->eventBus->dispatch(
            new OrderPlaced($order->getNumber(), $this->dateTimeProvider->__invoke())
        );
    }
}
