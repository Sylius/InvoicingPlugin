<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Converter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Converter\TaxItemsConverterInterface;

final class TaxItemsConverterSpec extends ObjectBehavior
{
    public function it_implements_tax_items_converter_interface(): void
    {
        $this->shouldImplement(TaxItemsConverterInterface::class);
    }

    public function it_extracts_tax_items_from_order(
        OrderInterface $order,
        AdjustmentInterface $taxAdjustment
    ): void {
        $order
            ->getAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment->getWrappedObject()]))
        ;

        $taxAdjustment->getLabel()->willReturn('VAT (10%)');
        $taxAdjustment->getAmount()->willReturn(500);

        $this->convert($order)->shouldReturnAnInstanceOf(Collection::class);
    }
}
