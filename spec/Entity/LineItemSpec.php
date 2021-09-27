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

namespace spec\Sylius\InvoicingPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Exception\LineItemsCannotBeMerged;

final class LineItemSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(
            'Mjolnir',
            2,
            5000,
            10000,
            1000,
            11000,
            'Blue',
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            '10%'
        );
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
        $this->id()->shouldReturn(null);
    }

    function it_implements_line_item_interface(): void
    {
        $this->shouldImplement(LineItemInterface::class);
    }

    function it_implements_resource_interface(): void
    {
        $this->shouldImplement(ResourceInterface::class);
    }

    function it_has_proper_line_item_data(): void
    {
        $this->name()->shouldReturn('Mjolnir');
        $this->quantity()->shouldReturn(2);
        $this->unitPrice()->shouldReturn(5000);
        $this->subtotal()->shouldReturn(10000);
        $this->taxTotal()->shouldReturn(1000);
        $this->total()->shouldReturn(11000);
        $this->variantName()->shouldReturn('Blue');
        $this->variantCode()->shouldReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');
    }

    function it_has_an_invoice(InvoiceInterface $invoice): void
    {
        $this->setInvoice($invoice);
        $this->invoice()->shouldReturn($invoice);
    }

    public function it_merges_with_another_line_item(LineItemInterface $newLineItem): void
    {
        $newLineItem->name()->willReturn('Mjolnir');
        $newLineItem->quantity()->willReturn(1);
        $newLineItem->unitPrice()->willReturn(5000);
        $newLineItem->subtotal()->willReturn(5000);
        $newLineItem->total()->willReturn(5500);
        $newLineItem->taxTotal()->willReturn(500);
        $newLineItem->taxRate()->willReturn('10%');

        $this->merge($newLineItem);

        $this->quantity()->shouldReturn(3);
        $this->subtotal()->shouldReturn(15000);
        $this->total()->shouldReturn(16500);
        $this->taxTotal()->shouldReturn(1500);
    }

    public function it_throws_an_exception_if_another_line_item_is_different_during_merging(LineItemInterface $newLineItem): void
    {
        $newLineItem->name()->willReturn('Stormbreaker');
        $newLineItem->unitPrice()->willReturn(5000);
        $newLineItem->taxRate()->willReturn('10%');

        $this->shouldThrow(LineItemsCannotBeMerged::class)->during('merge', [$newLineItem]);
    }

    public function it_compares_with_another_line_item(
        LineItemInterface $theSameLineItem,
        LineItemInterface $differentLineItem
    ): void {
        $theSameLineItem->name()->willReturn('Mjolnir');
        $theSameLineItem->unitPrice()->willReturn(5000);
        $theSameLineItem->taxRate()->willReturn('10%');

        $differentLineItem->name()->willReturn('Stormbreaker');
        $differentLineItem->unitPrice()->willReturn(5000);
        $differentLineItem->taxRate()->willReturn('10%');

        $this->compare($theSameLineItem)->shouldReturn(true);
        $this->compare($differentLineItem)->shouldReturn(false);
    }
}
