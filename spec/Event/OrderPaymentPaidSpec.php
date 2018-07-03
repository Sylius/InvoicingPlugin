<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Event;

use PhpSpec\ObjectBehavior;

final class OrderPaymentPaidSpec extends ObjectBehavior
{
    function it_represents_an_immutable_fact_that_payment_related_to_order_was_completed(): void
    {
        $date = new \DateTimeImmutable('now');
        $orderNumber = '000001';

        $this->beConstructedWith($orderNumber, $date);

        $this->orderNumber()->shouldBeEqualTo($orderNumber);
        $this->date()->shouldBeLike($date);
    }
}
