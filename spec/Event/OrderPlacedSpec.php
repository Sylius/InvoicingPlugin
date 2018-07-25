<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Event;

use PhpSpec\ObjectBehavior;

final class OrderPlacedSpec extends ObjectBehavior
{
    function it_represents_an_immutable_fact_that_an_order_has_been_placed(): void
    {
        $date = new \DateTimeImmutable();

        $this->beConstructedWith('000001', $date);

        $this->orderNumber()->shouldReturn('000001');
        $this->date()->shouldBeLike($date);
    }
}
