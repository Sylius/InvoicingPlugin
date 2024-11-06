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
use Sylius\InvoicingPlugin\Converter\TaxItemsConverterInterface;
use Sylius\InvoicingPlugin\Entity\TaxItemInterface;
use Sylius\InvoicingPlugin\Factory\TaxItemFactoryInterface;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProviderInterface;

final class TaxItemsConverterSpec extends ObjectBehavior
{
    function let(TaxRatePercentageProviderInterface $taxRatePercentageProvider, TaxItemFactoryInterface $taxItemFactory): void
    {
        $this->beConstructedWith($taxRatePercentageProvider, $taxItemFactory);
    }

    function it_implements_tax_items_converter_interface(): void
    {
        $this->shouldImplement(TaxItemsConverterInterface::class);
    }

    function it_extracts_tax_items_from_order(
        TaxItemFactoryInterface $taxItemFactory,
        TaxItemInterface $taxItem,
        TaxRatePercentageProviderInterface $taxRatePercentageProvider,
        OrderInterface $order,
        AdjustmentInterface $taxAdjustment,
    ): void {
        $taxItemFactory->createWithData('10%', 500)->willReturn($taxItem);

        $order
            ->getAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment->getWrappedObject()]))
        ;

        $taxRatePercentageProvider->provideFromAdjustment($taxAdjustment)->willReturn('10%');
        $taxAdjustment->getAmount()->willReturn(500);

        $this->convert($order)->shouldBeLike(new ArrayCollection([$taxItem->getWrappedObject()]));
    }
}
