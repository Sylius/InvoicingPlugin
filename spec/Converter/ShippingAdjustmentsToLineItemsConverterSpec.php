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
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\InvoicingPlugin\Converter\LineItemsConverterInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Factory\LineItemFactoryInterface;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProviderInterface;

final class ShippingAdjustmentsToLineItemsConverterSpec extends ObjectBehavior
{
    function let(TaxRatePercentageProviderInterface $taxRatePercentageProvider, LineItemFactoryInterface $lineItemFactory): void
    {
        $this->beConstructedWith($taxRatePercentageProvider, $lineItemFactory);
    }

    function it_implements_line_items_converter_interface(): void
    {
        $this->shouldImplement(LineItemsConverterInterface::class);
    }

    function it_extracts_line_items_from_orders_shipping_adjustments(
        TaxRatePercentageProviderInterface $taxRatePercentageProvider,
        LineItemFactoryInterface $lineItemFactory,
        LineItemInterface $lineItem,
        OrderInterface $order,
        AdjustmentInterface $shippingAdjustment,
        AdjustmentInterface $shippingTaxAdjustment,
        ShipmentInterface $shipment,
    ): void {
        $lineItemFactory->createWithData('UPS', 1, 800, 1000, 1000, 200, 1200, null, null, '20%')->willReturn($lineItem);

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

        $shipment->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT)->willReturn(200);

        $taxRatePercentageProvider->provideFromAdjustable($shipment)->willReturn('20%');

        $this->convert($order)->shouldBeLike([$lineItem]);
    }
}
