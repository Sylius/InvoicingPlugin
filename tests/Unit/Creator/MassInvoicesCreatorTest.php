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

namespace Tests\Sylius\InvoicingPlugin\Unit\Creator;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Creator\InvoiceCreatorInterface;
use Sylius\InvoicingPlugin\Creator\MassInvoicesCreator;
use Symfony\Component\Clock\ClockInterface;

final class MassInvoicesCreatorTest extends TestCase
{
    private InvoiceCreatorInterface&MockObject $invoiceCreator;

    private ClockInterface&MockObject $clock;

    private MassInvoicesCreator $massInvoicesCreator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceCreator = $this->createMock(InvoiceCreatorInterface::class);
        $this->clock = $this->createMock(ClockInterface::class);

        $this->massInvoicesCreator = new MassInvoicesCreator($this->invoiceCreator, $this->clock);
    }

    #[Test]
    public function it_requests_invoices_creation_for_multiple_orders(): void
    {
        $firstOrder = $this->createMock(OrderInterface::class);
        $secondOrder = $this->createMock(OrderInterface::class);
        $thirdOrder = $this->createMock(OrderInterface::class);

        $firstOrder->method('getNumber')->willReturn('0000001');
        $secondOrder->method('getNumber')->willReturn('0000002');
        $thirdOrder->method('getNumber')->willReturn('0000003');

        $firstInvoiceDateTime = new \DateTimeImmutable('2019-02-25');
        $secondInvoiceDateTime = new \DateTimeImmutable('2019-02-25');
        $thirdInvoiceDateTime = new \DateTimeImmutable('2019-02-25');

        $this->clock
            ->method('now')
            ->willReturnCallback(function () use ($firstInvoiceDateTime, $secondInvoiceDateTime, $thirdInvoiceDateTime) {
                static $callCount = 0;
                ++$callCount;

                if ($callCount === 1) {
                    return $firstInvoiceDateTime;
                }
                if ($callCount === 2) {
                    return $secondInvoiceDateTime;
                }

                return $thirdInvoiceDateTime;
            });

        $this->invoiceCreator
            ->expects($this->exactly(3))
            ->method('__invoke')
            ->willReturnCallback(function (...$args) use ($firstInvoiceDateTime, $secondInvoiceDateTime, $thirdInvoiceDateTime) {
                static $callCount = 0;
                ++$callCount;

                if ($callCount === 1) {
                    $this->assertEquals(['0000001', $firstInvoiceDateTime], $args);
                } elseif ($callCount === 2) {
                    $this->assertEquals(['0000002', $secondInvoiceDateTime], $args);
                } else {
                    $this->assertEquals(['0000003', $thirdInvoiceDateTime], $args);
                }
            });

        $this->massInvoicesCreator->__invoke([$firstOrder, $secondOrder, $thirdOrder]);
    }
}
