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

namespace spec\Sylius\InvoicingPlugin\Converter;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\InvoicingPlugin\Converter\LineItemsConverterInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Factory\LineItemFactoryInterface;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProviderInterface;
use Sylius\InvoicingPlugin\Provider\UnitNetPriceProviderInterface;

final class OrderItemUnitsToLineItemsConverterSpec extends ObjectBehavior
{
    function let(
        TaxRatePercentageProviderInterface $taxRatePercentageProvider,
        LineItemFactoryInterface $lineItemFactory,
        UnitNetPriceProviderInterface $unitNetPriceProvider,
    ): void {
        $this->beConstructedWith($taxRatePercentageProvider, $lineItemFactory, $unitNetPriceProvider);
    }

    function it_implements_line_items_converter_interface(): void
    {
        $this->shouldImplement(LineItemsConverterInterface::class);
    }

    function it_extracts_line_items_from_order_item_units(
        TaxRatePercentageProviderInterface $taxRatePercentageProvider,
        LineItemFactoryInterface $lineItemFactory,
        UnitNetPriceProviderInterface $unitNetPriceProvider,
        LineItemInterface $lineItem,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $orderItemUnit,
        ProductVariantInterface $variant,
    ): void {
        $lineItemFactory->createWithData('Mjolnir', 1, 6000, 5000, 5000, 500, 5500, null, 'CODE', '10%')->willReturn($lineItem);

        $order->getItemUnits()->willReturn(new ArrayCollection([$orderItemUnit->getWrappedObject()]));

        $orderItemUnit->getTaxTotal()->willReturn(500);
        $orderItemUnit->getTotal()->willReturn(5500);
        $orderItemUnit->getOrderItem()->willReturn($orderItem);
        $unitNetPriceProvider->getUnitNetPrice($orderItemUnit)->willReturn(6000);

        $orderItem->getProductName()->willReturn('Mjolnir');
        $orderItem->getVariant()->willReturn($variant);
        $orderItem->getVariantName()->willReturn(null);

        $variant->getCode()->willReturn('CODE');

        $taxRatePercentageProvider->provideFromAdjustable($orderItemUnit)->willReturn('10%');

        $this->convert($order)->shouldBeLike([$lineItem]);
    }

    function it_groups_the_same_line_items_during_extracting_order_item_units(
        TaxRatePercentageProviderInterface $taxRatePercentageProvider,
        LineItemFactoryInterface $lineItemFactory,
        UnitNetPriceProviderInterface $unitNetPriceProvider,
        LineItemInterface $mjolnirLineItem,
        LineItemInterface $stormbreakerLineItem,
        OrderInterface $order,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        OrderItemUnitInterface $firstOrderItemUnit,
        OrderItemUnitInterface $secondOrderItemUnit,
        OrderItemUnitInterface $thirdOrderItemUnit,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant,
    ): void {
        $lineItemFactory->createWithData('Mjolnir', 1, 5000, 5000, 5000, 500, 5500, null, 'MJOLNIR', '10%')->willReturn($mjolnirLineItem);
        $lineItemFactory->createWithData('Stormbreaker', 1, 8000, 8000, 8000, 1600, 9600, null, 'STORMBREAKER', '20%')->willReturn($stormbreakerLineItem);

        $mjolnirLineItem->compare($mjolnirLineItem)->willReturn(true);
        $mjolnirLineItem->compare($stormbreakerLineItem)->willReturn(false);

        $mjolnirLineItem->merge($mjolnirLineItem)->shouldBeCalled();
        $mjolnirLineItem->merge($stormbreakerLineItem)->shouldNotBeCalled();

        $order->getItemUnits()->willReturn(new ArrayCollection([
            $firstOrderItemUnit->getWrappedObject(),
            $secondOrderItemUnit->getWrappedObject(),
            $thirdOrderItemUnit->getWrappedObject(),
        ]));

        $firstOrderItemUnit->getTaxTotal()->willReturn(500);
        $firstOrderItemUnit->getTotal()->willReturn(5500);
        $firstOrderItemUnit->getOrderItem()->willReturn($firstOrderItem);
        $unitNetPriceProvider->getUnitNetPrice($firstOrderItemUnit)->willReturn(5000);

        $secondOrderItemUnit->getTaxTotal()->willReturn(500);
        $secondOrderItemUnit->getTotal()->willReturn(5500);
        $secondOrderItemUnit->getOrderItem()->willReturn($firstOrderItem);
        $unitNetPriceProvider->getUnitNetPrice($secondOrderItemUnit)->willReturn(5000);

        $thirdOrderItemUnit->getTaxTotal()->willReturn(1600);
        $thirdOrderItemUnit->getTotal()->willReturn(9600);
        $thirdOrderItemUnit->getOrderItem()->willReturn($secondOrderItem);
        $unitNetPriceProvider->getUnitNetPrice($thirdOrderItemUnit)->willReturn(8000);

        $firstOrderItem->getProductName()->willReturn('Mjolnir');
        $firstOrderItem->getVariant()->willReturn($firstVariant);
        $firstOrderItem->getVariantName()->willReturn(null);

        $secondOrderItem->getProductName()->willReturn('Stormbreaker');
        $secondOrderItem->getVariant()->willReturn($secondVariant);
        $secondOrderItem->getVariantName()->willReturn(null);

        $firstVariant->getCode()->willReturn('MJOLNIR');
        $secondVariant->getCode()->willReturn('STORMBREAKER');

        $taxRatePercentageProvider->provideFromAdjustable($firstOrderItemUnit)->willReturn('10%');
        $taxRatePercentageProvider->provideFromAdjustable($secondOrderItemUnit)->willReturn('10%');
        $taxRatePercentageProvider->provideFromAdjustable($thirdOrderItemUnit)->willReturn('20%');

        $this->convert($order)->shouldBeLike([$mjolnirLineItem, $stormbreakerLineItem]);
    }
}
