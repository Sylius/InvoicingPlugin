<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Unit\Event;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;

final class OrderPaymentPaidTest extends TestCase
{
    #[Test]
    public function it_represents_an_immutable_fact_that_payment_related_to_order_was_completed(): void
    {
        $date = new \DateTimeImmutable('now');

        $event = new OrderPaymentPaid('000001', $date);

        self::assertSame('000001', $event->orderNumber());
        self::assertEquals($date, $event->date());
    }
}
