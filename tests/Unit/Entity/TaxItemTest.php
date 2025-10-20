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
use Sylius\InvoicingPlugin\Entity\TaxItem;
use Sylius\InvoicingPlugin\Entity\TaxItemInterface;

final class TaxItemTest extends TestCase
{
    private TaxItem $taxItem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taxItem = new TaxItem('VAT (23%)', 2300);
    }

    #[Test]
    public function it_implements_tax_item_interface(): void
    {
        self::assertInstanceOf(TaxItemInterface::class, $this->taxItem);
    }

    #[Test]
    public function it_implements_resource_interface(): void
    {
        self::assertInstanceOf(ResourceInterface::class, $this->taxItem);
    }

    #[Test]
    public function it_has_proper_tax_item_data(): void
    {
        self::assertSame('VAT (23%)', $this->taxItem->label());
        self::assertSame(2300, $this->taxItem->amount());
    }

    #[Test]
    public function it_has_an_invoice(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);

        $this->taxItem->setInvoice($invoice);

        self::assertSame($invoice, $this->taxItem->invoice());
    }
}
