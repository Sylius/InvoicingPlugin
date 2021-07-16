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
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\InvoicingPlugin\Converter\LineItemsConverterInterface;

final class LineItemsConverterSpec extends ObjectBehavior
{
    function it_implements_line_items_converter_interface(): void
    {
        $this->shouldImplement(LineItemsConverterInterface::class);
    }

    function it_extracts_line_items_from_order(
        OrderInterface $order,
        OrderItemInterface $orderItem,
        AdjustmentInterface $shippingAdjustment,
        ProductVariantInterface $variant
    ): void {
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $order
            ->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$shippingAdjustment->getWrappedObject()]))
        ;

        $orderItem->getProductName()->willReturn('Mjolnir');
        $orderItem->getQuantity()->willReturn(2);
        $orderItem->getUnitPrice()->willReturn(5000);
        $orderItem->getSubtotal()->willReturn(10000);
        $orderItem->getTaxTotal()->willReturn(500);
        $orderItem->getTotal()->willReturn(10300);
        $orderItem->getVariantName()->willReturn('Blue');
        $orderItem->getVariant()->willReturn($variant);

        $variant->getCode()->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $shippingAdjustment->getLabel()->willReturn('UPS');
        $shippingAdjustment->getAmount()->willReturn(800);

        $order
            ->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$shippingAdjustment->getWrappedObject()]))
        ;

        $this->convert($order)->shouldReturnAnInstanceOf(Collection::class);
    }
}
