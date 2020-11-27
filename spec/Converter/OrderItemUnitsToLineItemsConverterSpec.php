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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\InvoicingPlugin\Converter\LineItemsConverterInterface;
use Sylius\InvoicingPlugin\Entity\LineItem;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProviderInterface;

final class OrderItemUnitsToLineItemsConverterSpec extends ObjectBehavior
{
    function let(TaxRatePercentageProviderInterface $taxRatePercentageProvider): void
    {
        $this->beConstructedWith(LineItem::class, $taxRatePercentageProvider);
    }

    function it_implements_line_items_converter_interface(): void
    {
        $this->shouldImplement(LineItemsConverterInterface::class);
    }

    function it_extracts_line_items_from_order_item_units(
        TaxRatePercentageProviderInterface $taxRatePercentageProvider,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $orderItemUnit,
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

        $taxRatePercentageProvider->provideFromAdjustable($orderItemUnit)->willReturn('10%');

        $this->convert($order)->shouldBeLike([
            new LineItem('Mjolnir', 1, 5000, 5000, 500, 5500, null, 'CODE', '10%'),
        ]);
    }

    function it_groups_the_same_line_items_during_extracting_order_item_units(
        TaxRatePercentageProviderInterface $taxRatePercentageProvider,
        OrderInterface $order,
        OrderItemInterface $firstOrderItem,
        OrderItemInterface $secondOrderItem,
        OrderItemUnitInterface $firstOrderItemUnit,
        OrderItemUnitInterface $secondOrderItemUnit,
        OrderItemUnitInterface $thirdOrderItemUnit,
        ProductVariantInterface $firstVariant,
        ProductVariantInterface $secondVariant
    ): void {
        $order->getItemUnits()->willReturn(new ArrayCollection([
            $firstOrderItemUnit->getWrappedObject(),
            $secondOrderItemUnit->getWrappedObject(),
            $thirdOrderItemUnit->getWrappedObject(),
        ]));

        $firstOrderItemUnit->getTaxTotal()->willReturn(500);
        $firstOrderItemUnit->getTotal()->willReturn(5500);
        $firstOrderItemUnit->getOrderItem()->willReturn($firstOrderItem);

        $secondOrderItemUnit->getTaxTotal()->willReturn(500);
        $secondOrderItemUnit->getTotal()->willReturn(5500);
        $secondOrderItemUnit->getOrderItem()->willReturn($firstOrderItem);

        $thirdOrderItemUnit->getTaxTotal()->willReturn(1600);
        $thirdOrderItemUnit->getTotal()->willReturn(9600);
        $thirdOrderItemUnit->getOrderItem()->willReturn($secondOrderItem);

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

        $this->convert($order)->shouldBeLike([
            new LineItem('Mjolnir', 2, 5000, 10000, 1000, 11000, null, 'MJOLNIR', '10%'),
            new LineItem('Stormbreaker', 1, 8000, 8000, 1600, 9600, null, 'STORMBREAKER', '20%'),
        ]);
    }
}
