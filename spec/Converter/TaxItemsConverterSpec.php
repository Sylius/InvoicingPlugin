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
use Sylius\InvoicingPlugin\Converter\TaxItemsConverterInterface;
use Sylius\InvoicingPlugin\Entity\TaxItem;
use Sylius\InvoicingPlugin\Provider\TaxRateProviderInterface;

final class TaxItemsConverterSpec extends ObjectBehavior
{
    function let(TaxRateProviderInterface $taxRateProvider): void
    {
        $this->beConstructedWith($taxRateProvider);
    }

    function it_implements_tax_items_converter_interface(): void
    {
        $this->shouldImplement(TaxItemsConverterInterface::class);
    }

    function it_extracts_tax_items_from_order(
        TaxRateProviderInterface $taxRateProvider,
        OrderInterface $order,
        AdjustmentInterface $taxAdjustment
    ): void {
        $order
            ->getAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment->getWrappedObject()]))
        ;

        $taxRateProvider->provideFromAdjustment($taxAdjustment)->willReturn('10%');
        $taxAdjustment->getAmount()->willReturn(500);

        $this->convert($order)->shouldBeLike(new ArrayCollection([new TaxItem('10%', 500)]));
    }
}
