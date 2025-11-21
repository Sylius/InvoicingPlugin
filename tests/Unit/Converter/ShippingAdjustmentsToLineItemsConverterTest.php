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

namespace Tests\Sylius\InvoicingPlugin\Unit\Converter;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\InvoicingPlugin\Converter\LineItemsConverterInterface;
use Sylius\InvoicingPlugin\Converter\ShippingAdjustmentsToLineItemsConverter;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Factory\LineItemFactoryInterface;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProviderInterface;

final class ShippingAdjustmentsToLineItemsConverterTest extends TestCase
{
    private MockObject&TaxRatePercentageProviderInterface $taxRatePercentageProvider;

    private LineItemFactoryInterface&MockObject $lineItemFactory;

    private ShippingAdjustmentsToLineItemsConverter $converter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taxRatePercentageProvider = $this->createMock(TaxRatePercentageProviderInterface::class);
        $this->lineItemFactory = $this->createMock(LineItemFactoryInterface::class);

        $this->converter = new ShippingAdjustmentsToLineItemsConverter(
            $this->taxRatePercentageProvider,
            $this->lineItemFactory,
        );
    }

    #[Test]
    public function it_implements_line_items_converter_interface(): void
    {
        self::assertInstanceOf(LineItemsConverterInterface::class, $this->converter);
    }

    #[Test]
    public function it_extracts_line_items_from_orders_shipping_adjustments(): void
    {
        $lineItem = $this->createMock(LineItemInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $shippingAdjustment = $this->createMock(AdjustmentInterface::class);
        $shippingTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $shipment = $this->createMock(ShipmentInterface::class);

        $this->lineItemFactory
            ->expects(self::once())
            ->method('createWithData')
            ->with('UPS', 1, 800, 1000, 1000, 200, 1200, null, null, '20%')
            ->willReturn($lineItem);

        $order
            ->expects(self::once())
            ->method('getAdjustments')
            ->with(AdjustmentInterface::SHIPPING_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$shippingAdjustment]));

        $shippingAdjustment->expects(self::once())->method('getLabel')->willReturn('UPS');
        $shippingAdjustment->expects(self::once())->method('getShipment')->willReturn($shipment);

        $shipment
            ->expects(self::once())
            ->method('getAdjustments')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$shippingTaxAdjustment]));

        $shippingTaxAdjustment->expects(self::once())->method('getAmount')->willReturn(200);

        $shipment
            ->method('getAdjustmentsTotal')
            ->willReturnCallback(function ($type = null) {
                if ($type === 'order_shipping_promotion') {
                    return 200;
                }

                return 1200; // Default case (when called with null or no parameter)
            });

        $this->taxRatePercentageProvider
            ->expects(self::once())
            ->method('provideFromAdjustable')
            ->with($shipment)
            ->willReturn('20%');

        $result = $this->converter->convert($order);

        self::assertEquals([$lineItem], $result);
    }
}
