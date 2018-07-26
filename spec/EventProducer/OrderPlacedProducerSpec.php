<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\EventProducer;

use PhpSpec\ObjectBehavior;
use Prooph\ServiceBus\EventBus;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Event\OrderPlaced;

final class OrderPlacedProducerSpec extends ObjectBehavior
{
    function let(EventBus $eventBus, DateTimeProvider $dateTimeProvider): void
    {
        $this->beConstructedWith($eventBus, $dateTimeProvider);
    }

    function it_dispatches_an_order_placed_event_for_an_order(
        EventBus $eventBus,
        DateTimeProvider $dateTimeProvider,
        OrderInterface $order,
        \DateTime $dateTime
    ): void {
        $dateTimeProvider->__invoke()->willReturn($dateTime);

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

        $this($order);
    }
}
