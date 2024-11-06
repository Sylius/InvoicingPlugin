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
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\InvoicingPlugin\Exception\MoreThanOneTaxAdjustment;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProviderInterface;

final class TaxRatePercentageProviderSpec extends ObjectBehavior
{
    public function it_implements_tax_rate_percentage_provider_interface(): void
    {
        $this->shouldImplement(TaxRatePercentageProviderInterface::class);
    }

    public function it_provides_a_tax_rate_from_adjustable_entity(
        OrderItemUnitInterface $orderItemUnit,
        AdjustmentInterface $taxAdjustment,
    ): void {
        $orderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment->getWrappedObject()]))
        ;

        $taxAdjustment->getDetails()->willReturn(['taxRateAmount' => 0.2]);

        $this->provideFromAdjustable($orderItemUnit)->shouldReturn('20%');
    }

    public function it_returns_null_if_there_is_no_tax_adjustment_on_adjustable_entity(OrderItemUnitInterface $orderItemUnit): void
    {
        $orderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([]))
        ;

        $this->provideFromAdjustable($orderItemUnit)->shouldReturn(null);
    }

    public function it_throws_an_exception_if_there_is_no_tax_rate_amount_in_details_of_adjustment(
        OrderItemUnitInterface $orderItemUnit,
        AdjustmentInterface $taxAdjustment,
    ): void {
        $orderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment->getWrappedObject()]))
        ;

        $taxAdjustment->getDetails()->willReturn([]);

        $this->shouldThrow(\InvalidArgumentException::class)->during('provideFromAdjustable', [$orderItemUnit]);
    }

    public function it_throws_an_exception_if_order_item_unit_has_more_adjustments_than_one(
        OrderItemUnitInterface $orderItemUnit,
        AdjustmentInterface $firstTaxAdjustment,
        AdjustmentInterface $secondTaxAdjustment,
    ): void {
        $orderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$firstTaxAdjustment->getWrappedObject(), $secondTaxAdjustment->getWrappedObject()]))
        ;

        $this->shouldThrow(MoreThanOneTaxAdjustment::class)->during('provideFromAdjustable', [$orderItemUnit]);
    }

    public function it_provides_a_tax_rate_from_adjustment(
        AdjustmentInterface $taxAdjustment,
    ): void {
        $taxAdjustment->getDetails()->willReturn(['taxRateAmount' => 0.2]);

        $this->provideFromAdjustment($taxAdjustment)->shouldReturn('20%');
    }
}
