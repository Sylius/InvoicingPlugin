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

final class OrderPaymentPaidSpec extends ObjectBehavior
{
    public function it_represents_an_immutable_fact_that_payment_related_to_order_was_completed(): void
    {
        $date = new \DateTimeImmutable('now');

        $this->beConstructedWith('000001', $date);

        $this->orderNumber()->shouldReturn('000001');
        $this->date()->shouldBeLike($date);
    }
}
