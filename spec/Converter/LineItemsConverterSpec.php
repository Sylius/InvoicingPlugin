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

namespace spec\Sylius\InvoicingPlugin\Converter;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\InvoicingPlugin\Converter\LineItemsConverterInterface;
use Sylius\InvoicingPlugin\Entity\LineItem;
use Sylius\InvoicingPlugin\Provider\TaxRateProviderInterface;

final class LineItemsConverterSpec extends ObjectBehavior
{
    function let(TaxRateProviderInterface $taxRateProvider): void
    {
        $this->beConstructedWith($taxRateProvider);
    }

    function it_implements_line_items_converter_interface(): void
    {
        $this->shouldImplement(LineItemsConverterInterface::class);
    }

    function it_extracts_line_items_from_order(
        TaxRateProviderInterface $taxRateProvider,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $orderItemUnit,
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $shippingTaxAdjustment,
        ShipmentInterface $shipment,
        ProductVariantInterface $variant
    ): void {
        $order->getItemUnits()->willReturn(new ArrayCollection([$orderItemUnit->getWrappedObject()]));

        $orderItemUnit->getTaxTotal()->willReturn(500);
        $orderItemUnit->getTotal()->willReturn(5500);
        $orderItemUnit->getOrderItem()->willReturn($orderItem);

        $orderItem->getProductName()->willReturn('Mjolnir');
        $orderItem->getVariant()->willReturn($variant);
        $orderItem->getVariantName()->willReturn(null);

        $variant->getCode()->willReturn('CODE');

        $taxRateProvider->provide($orderItemUnit)->willReturn('10%');

        $order
            ->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$shippingAdjustment->getWrappedObject()]))
        ;

        $shippingAdjustment->getLabel()->willReturn('UPS');
        $shippingAdjustment->getShipment()->willReturn($shipment);

        $shipment->getAdjustmentsTotal()->willReturn(1200);
        $shipment
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$shippingTaxAdjustment->getWrappedObject()]))
        ;

        $shippingTaxAdjustment->getAmount()->willReturn(200);

        $taxRateProvider->provide($shipment)->willReturn('20%');

        $this->convert($order)->shouldBeLike(new ArrayCollection([
            new LineItem('Mjolnir', 1, 5000, 5000, 500, 5500, null, 'CODE', '10%'),
            new LineItem('UPS', 1, 1000, 1000, 200, 1200, null, null, '20%')
        ]));
    }

    function it_groups_the_same_line_items_during_extracting(
        TaxRateProviderInterface $taxRateProvider,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $firstOrderItemUnit,
        OrderItemUnitInterface $secondOrderItemUnit,
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $shippingTaxAdjustment,
        ShipmentInterface $shipment,
        ProductVariantInterface $variant
    ): void {
        $order->getItemUnits()->willReturn(new ArrayCollection([
            $firstOrderItemUnit->getWrappedObject(),
            $secondOrderItemUnit->getWrappedObject()
        ]));

        $firstOrderItemUnit->getTaxTotal()->willReturn(500);
        $firstOrderItemUnit->getTotal()->willReturn(5500);
        $firstOrderItemUnit->getOrderItem()->willReturn($orderItem);

        $secondOrderItemUnit->getTaxTotal()->willReturn(500);
        $secondOrderItemUnit->getTotal()->willReturn(5500);
        $secondOrderItemUnit->getOrderItem()->willReturn($orderItem);

        $orderItem->getProductName()->willReturn('Mjolnir');
        $orderItem->getVariant()->willReturn($variant);
        $orderItem->getVariantName()->willReturn(null);

        $variant->getCode()->willReturn('CODE');

        $taxRateProvider->provide($firstOrderItemUnit)->willReturn('10%');
        $taxRateProvider->provide($secondOrderItemUnit)->willReturn('10%');

        $order
            ->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$shippingAdjustment->getWrappedObject()]))
        ;

        $shippingAdjustment->getLabel()->willReturn('UPS');
        $shippingAdjustment->getShipment()->willReturn($shipment);

        $shipment->getAdjustmentsTotal()->willReturn(1200);
        $shipment
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$shippingTaxAdjustment->getWrappedObject()]))
        ;

        $shippingTaxAdjustment->getAmount()->willReturn(200);

        $taxRateProvider->provide($shipment)->willReturn('20%');

        $this->convert($order)->shouldBeLike(new ArrayCollection([
            new LineItem('Mjolnir', 2, 5000, 10000, 1000, 11000, null, 'CODE', '10%'),
            new LineItem('UPS', 1, 1000, 1000, 200, 1200, null, null, '20%')
        ]));
    }
}
