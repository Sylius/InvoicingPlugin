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
use Sylius\InvoicingPlugin\Event\OrderPlaced;

final class OrderPlacedTest extends TestCase
{
    #[Test]
    public function it_represents_an_immutable_fact_that_an_order_has_been_placed(): void
    {
        $date = new \DateTimeImmutable();

        $event = new OrderPlaced('000001', $date);

        self::assertSame('000001', $event->orderNumber());
        self::assertEquals($date, $event->date());
    }
}
