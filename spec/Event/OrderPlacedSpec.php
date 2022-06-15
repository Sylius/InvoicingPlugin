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

namespace spec\Sylius\InvoicingPlugin\Event;

use PhpSpec\ObjectBehavior;

final class OrderPlacedSpec extends ObjectBehavior
{
    function it_represents_an_immutable_fact_that_an_order_has_been_placed(): void
    {
        $date = new \DateTimeImmutable();

        $this->beConstructedWith(1, $date);

        $this->orderId()->shouldReturn(1);
        $this->date()->shouldBeLike($date);
    }
}
