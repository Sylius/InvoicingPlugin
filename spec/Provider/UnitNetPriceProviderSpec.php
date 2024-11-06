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

namespace spec\Sylius\InvoicingPlugin\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\InvoicingPlugin\Provider\UnitNetPriceProviderInterface;

final class UnitNetPriceProviderSpec extends ObjectBehavior
{
    function it_implements_unit_net_price_provider_interface(): void
    {
        $this->shouldImplement(UnitNetPriceProviderInterface::class);
    }

    function it_provides_net_price_for_unit_with_taxes_included_in_price(
        OrderItemUnitInterface $unit,
        OrderItemInterface $orderItem,
        AdjustmentInterface $taxAdjustment,
    ): void {
        $unit->getOrderItem()->willReturn($orderItem);
        $orderItem->getUnitPrice()->willReturn(1000);

        $unit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment->getWrappedObject()]))
        ;
        $taxAdjustment->isNeutral()->willReturn(true);
        $taxAdjustment->getAmount()->willReturn(200);

        $this->getUnitNetPrice($unit)->shouldReturn(800);
    }

    function it_provides_net_price_for_unit_with_taxes_excluded_of_price(
        OrderItemUnitInterface $unit,
        OrderItemInterface $orderItem,
        AdjustmentInterface $taxAdjustment,
    ): void {
        $unit->getOrderItem()->willReturn($orderItem);
        $orderItem->getUnitPrice()->willReturn(1000);

        $unit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment->getWrappedObject()]))
        ;
        $taxAdjustment->isNeutral()->willReturn(false);
        $taxAdjustment->getAmount()->willReturn(200);

        $this->getUnitNetPrice($unit)->shouldReturn(1000);
    }
}
