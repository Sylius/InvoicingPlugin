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

namespace Tests\Sylius\InvoicingPlugin\Unit\Entity;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\LineItem;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Exception\LineItemsCannotBeMerged;

final class LineItemTest extends TestCase
{
    private LineItem $lineItem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->lineItem = new LineItem(
            'Mjolnir',
            2,
            5000,
            5000,
            10000,
            1000,
            11000,
            'Blue',
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            '10%',
        );
    }

    #[Test]
    public function it_implements_line_item_interface(): void
    {
        self::assertInstanceOf(LineItemInterface::class, $this->lineItem);
    }

    #[Test]
    public function it_implements_resource_interface(): void
    {
        self::assertInstanceOf(ResourceInterface::class, $this->lineItem);
    }

    #[Test]
    public function it_has_proper_line_item_data(): void
    {
        self::assertSame('Mjolnir', $this->lineItem->name());
        self::assertSame(2, $this->lineItem->quantity());
        self::assertSame(5000, $this->lineItem->unitPrice());
        self::assertSame(5000, $this->lineItem->discountedUnitNetPrice());
        self::assertSame(10000, $this->lineItem->subtotal());
        self::assertSame(1000, $this->lineItem->taxTotal());
        self::assertSame(11000, $this->lineItem->total());
        self::assertSame('Blue', $this->lineItem->variantName());
        self::assertSame('7903c83a-4c5e-4bcf-81d8-9dc304c6a353', $this->lineItem->variantCode());
    }

    #[Test]
    public function it_has_an_invoice(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);

        $this->lineItem->setInvoice($invoice);

        $this->assertSame($invoice, $this->lineItem->invoice());
    }

    #[Test]
    public function it_merges_with_another_line_item(): void
    {
        $newLineItem = $this->createMock(LineItemInterface::class);
        $newLineItem->method('name')->willReturn('Mjolnir');
        $newLineItem->method('quantity')->willReturn(1);
        $newLineItem->method('unitPrice')->willReturn(5000);
        $newLineItem->method('discountedUnitNetPrice')->willReturn(5000);
        $newLineItem->method('subtotal')->willReturn(5000);
        $newLineItem->method('total')->willReturn(5500);
        $newLineItem->method('taxTotal')->willReturn(500);
        $newLineItem->method('taxRate')->willReturn('10%');
        $newLineItem->method('variantCode')->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $this->lineItem->merge($newLineItem);

        $this->assertSame(3, $this->lineItem->quantity());
        $this->assertSame(15000, $this->lineItem->subtotal());
        $this->assertSame(16500, $this->lineItem->total());
        $this->assertSame(1500, $this->lineItem->taxTotal());
    }

    #[Test]
    public function it_throws_an_exception_if_another_line_item_is_different_during_merging(): void
    {
        $newLineItem = $this->createMock(LineItemInterface::class);
        $newLineItem->method('name')->willReturn('Stormbreaker');
        $newLineItem->method('unitPrice')->willReturn(5000);
        $newLineItem->method('taxRate')->willReturn('10%');

        $this->expectException(LineItemsCannotBeMerged::class);

        $this->lineItem->merge($newLineItem);
    }

    #[Test]
    public function it_compares_with_another_line_item(): void
    {
        $theSameLineItem = $this->createMock(LineItemInterface::class);
        $theSameLineItem->method('name')->willReturn('Mjolnir');
        $theSameLineItem->method('unitPrice')->willReturn(5000);
        $theSameLineItem->method('discountedUnitNetPrice')->willReturn(5000);
        $theSameLineItem->method('taxRate')->willReturn('10%');
        $theSameLineItem->method('variantCode')->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $differentLineItemName = $this->createMock(LineItemInterface::class);
        $differentLineItemName->method('name')->willReturn('Stormbreaker');
        $differentLineItemName->method('unitPrice')->willReturn(5000);
        $differentLineItemName->method('discountedUnitNetPrice')->willReturn(5000);
        $differentLineItemName->method('taxRate')->willReturn('10%');
        $differentLineItemName->method('variantCode')->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $differentLineItemCode = $this->createMock(LineItemInterface::class);
        $differentLineItemCode->method('name')->willReturn('Mjolnir');
        $differentLineItemCode->method('unitPrice')->willReturn(5000);
        $differentLineItemCode->method('discountedUnitNetPrice')->willReturn(5000);
        $differentLineItemCode->method('taxRate')->willReturn('10%');
        $differentLineItemCode->method('variantCode')->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a350');

        $this->assertTrue($this->lineItem->compare($theSameLineItem));
        $this->assertFalse($this->lineItem->compare($differentLineItemName));
        $this->assertFalse($this->lineItem->compare($differentLineItemCode));
    }
}
