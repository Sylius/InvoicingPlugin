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
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Creator\InvoiceCreator;
use Sylius\InvoicingPlugin\Creator\InvoiceCreatorInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Exception\InvoiceAlreadyGenerated;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;

final class InvoiceCreatorTest extends TestCase
{
    private InvoiceRepositoryInterface&MockObject $invoiceRepository;

    private MockObject&OrderRepositoryInterface $orderRepository;

    private InvoiceGeneratorInterface&MockObject $invoiceGenerator;

    private InvoiceCreator $creator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->invoiceGenerator = $this->createMock(InvoiceGeneratorInterface::class);

        $this->creator = new InvoiceCreator(
            $this->invoiceRepository,
            $this->orderRepository,
            $this->invoiceGenerator,
        );
    }

    #[Test]
    public function it_implements_invoice_for_order_creator_interface(): void
    {
        self::assertInstanceOf(InvoiceCreatorInterface::class, $this->creator);
    }

    #[Test]
    public function it_creates_invoice_for_order(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $invoice = $this->createMock(InvoiceInterface::class);
        $invoiceDateTime = new \DateTimeImmutable('2019-02-25');

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('0000001')
            ->willReturn($order);

        $this->invoiceRepository
            ->expects(self::once())
            ->method('findOneByOrder')
            ->with($order)
            ->willReturn(null);

        $this->invoiceGenerator
            ->expects(self::once())
            ->method('generateForOrder')
            ->with($order, $invoiceDateTime)
            ->willReturn($invoice);

        $this->invoiceRepository
            ->expects(self::once())
            ->method('add')
            ->with($invoice);

        ($this->creator)('0000001', $invoiceDateTime);
    }

    #[Test]
    public function it_throws_an_exception_when_invoice_was_already_created_for_given_order(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $invoice = $this->createMock(InvoiceInterface::class);
        $invoiceDateTime = new \DateTimeImmutable('2019-02-25');

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('0000001')
            ->willReturn($order);

        $this->invoiceRepository
            ->expects(self::once())
            ->method('findOneByOrder')
            ->with($order)
            ->willReturn($invoice);

        $this->invoiceGenerator
            ->expects($this->never())
            ->method('generateForOrder');

        $this->invoiceRepository
            ->expects($this->never())
            ->method('add');

        $this->expectException(InvoiceAlreadyGenerated::class);
        $this->expectExceptionMessage('An invoice for order with number 0000001 was already generated');

        ($this->creator)('0000001', $invoiceDateTime);
    }
}
