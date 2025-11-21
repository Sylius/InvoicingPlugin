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

namespace Tests\Sylius\InvoicingPlugin\Unit\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;
use Sylius\InvoicingPlugin\Entity\TaxItem;
use Sylius\InvoicingPlugin\Factory\TaxItemFactory;
use Sylius\InvoicingPlugin\Factory\TaxItemFactoryInterface;

final class TaxItemFactoryTest extends TestCase
{
    private TaxItemFactory $taxItemFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->taxItemFactory = new TaxItemFactory(TaxItem::class);
    }

    #[Test]
    public function it_implements_tax_item_factory_interface(): void
    {
        self::assertInstanceOf(TaxItemFactoryInterface::class, $this->taxItemFactory);
    }

    #[Test]
    public function it_does_not_allow_to_create_with_empty_data(): void
    {
        $this->expectException(UnsupportedMethodException::class);

        $this->taxItemFactory->createNew();
    }

    #[Test]
    public function it_allows_only_for_injection_of_fqcn_that_are_tax_item_or_its_descendants(): void
    {
        $this->expectException(\DomainException::class);

        new TaxItemFactory(\stdClass::class);
    }

    #[Test]
    public function it_creates_tax_item_from_label_and_amount(): void
    {
        $result = $this->taxItemFactory->createWithData('Tax', 17);
        $expected = new TaxItem('Tax', 17);

        self::assertEquals($expected, $result);
    }
}
