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

namespace Tests\Sylius\InvoicingPlugin\Unit\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\InvoicingPlugin\Converter\LineItemsConverterInterface;
use Sylius\InvoicingPlugin\Converter\TaxItemsConverterInterface;
use Sylius\InvoicingPlugin\Entity\BillingData;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Entity\TaxItemInterface;
use Sylius\InvoicingPlugin\Factory\BillingDataFactoryInterface;
use Sylius\InvoicingPlugin\Factory\InvoiceFactoryInterface;
use Sylius\InvoicingPlugin\Factory\InvoiceShopBillingDataFactoryInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceGenerator;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceIdentifierGenerator;
use Sylius\InvoicingPlugin\Generator\InvoiceNumberGenerator;

final class InvoiceGeneratorTest extends TestCase
{
    private InvoiceIdentifierGenerator&MockObject $uuidInvoiceIdentifierGenerator;

    private InvoiceNumberGenerator&MockObject $sequentialInvoiceNumberGenerator;

    private InvoiceFactoryInterface&MockObject $invoiceFactory;

    private BillingDataFactoryInterface&MockObject $billingDataFactory;

    private InvoiceShopBillingDataFactoryInterface&MockObject $invoiceShopBillingDataFactory;

    private LineItemsConverterInterface&MockObject $orderItemUnitsToLineItemsConverter;

    private LineItemsConverterInterface&MockObject $shippingAdjustmentsToLineItemsConverter;

    private MockObject&TaxItemsConverterInterface $taxItemsConverter;

    private InvoiceGenerator $invoiceGenerator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->uuidInvoiceIdentifierGenerator = $this->createMock(InvoiceIdentifierGenerator::class);
        $this->sequentialInvoiceNumberGenerator = $this->createMock(InvoiceNumberGenerator::class);
        $this->invoiceFactory = $this->createMock(InvoiceFactoryInterface::class);
        $this->billingDataFactory = $this->createMock(BillingDataFactoryInterface::class);
        $this->invoiceShopBillingDataFactory = $this->createMock(InvoiceShopBillingDataFactoryInterface::class);
        $this->orderItemUnitsToLineItemsConverter = $this->createMock(LineItemsConverterInterface::class);
        $this->shippingAdjustmentsToLineItemsConverter = $this->createMock(LineItemsConverterInterface::class);
        $this->taxItemsConverter = $this->createMock(TaxItemsConverterInterface::class);

        $this->invoiceGenerator = new InvoiceGenerator(
            $this->uuidInvoiceIdentifierGenerator,
            $this->sequentialInvoiceNumberGenerator,
            $this->invoiceFactory,
            $this->billingDataFactory,
            $this->invoiceShopBillingDataFactory,
            $this->orderItemUnitsToLineItemsConverter,
            $this->shippingAdjustmentsToLineItemsConverter,
            $this->taxItemsConverter,
        );
    }

    #[Test]
    public function it_is_an_invoice_generator(): void
    {
        self::assertInstanceOf(InvoiceGeneratorInterface::class, $this->invoiceGenerator);
    }

    #[Test]
    public function it_generates_an_invoice_for_a_given_order(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $billingAddress = $this->createMock(AddressInterface::class);
        $channel = $this->createMock(ChannelInterface::class);
        $invoiceShopBillingData = $this->createMock(InvoiceShopBillingDataInterface::class);
        $billingData = $this->createMock(BillingData::class);
        $invoice = $this->createMock(InvoiceInterface::class);
        $unitLineItem = $this->createMock(LineItemInterface::class);
        $shippingLineItem = $this->createMock(LineItemInterface::class);
        $taxItem = $this->createMock(TaxItemInterface::class);

        $date = new \DateTimeImmutable('2019-03-06');

        $this->uuidInvoiceIdentifierGenerator
            ->expects(self::once())
            ->method('generate')
            ->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $this->sequentialInvoiceNumberGenerator
            ->expects(self::once())
            ->method('generate')
            ->willReturn($date->format('Y/m') . '/0000001');

        $order->method('getCurrencyCode')->willReturn('USD');
        $order->method('getLocaleCode')->willReturn('en_US');
        $order->method('getTotal')->willReturn(10300);
        $order->method('getChannel')->willReturn($channel);
        $order->method('getPaymentState')->willReturn(OrderPaymentStates::STATE_PAID);
        $order->method('getBillingAddress')->willReturn($billingAddress);

        $this->billingDataFactory
            ->expects(self::once())
            ->method('createFromAddress')
            ->with($billingAddress)
            ->willReturn($billingData);

        $this->invoiceShopBillingDataFactory
            ->expects(self::once())
            ->method('createFromChannel')
            ->with($channel)
            ->willReturn($invoiceShopBillingData);

        $this->orderItemUnitsToLineItemsConverter
            ->expects(self::once())
            ->method('convert')
            ->with($order)
            ->willReturn([$unitLineItem]);

        $this->shippingAdjustmentsToLineItemsConverter
            ->expects(self::once())
            ->method('convert')
            ->with($order)
            ->willReturn([$shippingLineItem]);

        $this->taxItemsConverter
            ->expects(self::once())
            ->method('convert')
            ->with($order)
            ->willReturn(new ArrayCollection([$taxItem]));

        $this->invoiceFactory
            ->expects(self::once())
            ->method('createForData')
            ->with(
                '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
                '2019/03/0000001',
                $order,
                $date,
                $billingData,
                'USD',
                'en_US',
                10300,
                new ArrayCollection([$unitLineItem, $shippingLineItem]),
                new ArrayCollection([$taxItem]),
                $channel,
                InvoiceInterface::PAYMENT_STATE_COMPLETED,
                $invoiceShopBillingData,
            )
            ->willReturn($invoice);

        $result = $this->invoiceGenerator->generateForOrder($order, $date);

        self::assertSame($invoice, $result);
    }
}
