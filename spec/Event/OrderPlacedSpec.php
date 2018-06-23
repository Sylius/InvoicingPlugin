<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Event;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderPlacedSpec extends ObjectBehavior
{
    function it_represents_an_immutable_fact_that_an_order_has_been_placed(
        OrderInterface $order
    ): void {
        $date = new \DateTimeImmutable('now');

        $this->beConstructedWith($order, $date);

        $this->order()->shouldBeLike($order);
        $this->date()->shouldBeLike($date);
    }
}
