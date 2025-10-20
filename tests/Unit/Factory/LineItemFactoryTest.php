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
use Sylius\InvoicingPlugin\Entity\LineItem;
use Sylius\InvoicingPlugin\Factory\LineItemFactory;
use Sylius\InvoicingPlugin\Factory\LineItemFactoryInterface;

final class LineItemFactoryTest extends TestCase
{
    private LineItemFactory $lineItemFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->lineItemFactory = new LineItemFactory(LineItem::class);
    }

    #[Test]
    public function it_implements_line_item_factory_interface(): void
    {
        self::assertInstanceOf(LineItemFactoryInterface::class, $this->lineItemFactory);
    }

    #[Test]
    public function it_does_not_allow_to_create_with_empty_data(): void
    {
        $this->expectException(UnsupportedMethodException::class);

        $this->lineItemFactory->createNew();
    }

    #[Test]
    public function it_allows_only_for_injection_of_fqcn_that_are_line_item_or_its_descendants(): void
    {
        $this->expectException(\DomainException::class);

        new LineItemFactory(\stdClass::class);
    }

    #[Test]
    public function it_creates_line_items_with_data(): void
    {
        $result1 = $this->lineItemFactory->createWithData('Mjolnir', 2, 6000, 5000, 10000, 1000, 11000, null, 'MJOLNIR', '10%');
        $expected1 = new LineItem(
            'Mjolnir',
            2,
            6000,
            5000,
            10000,
            1000,
            11000,
            null,
            'MJOLNIR',
            '10%',
        );

        self::assertEquals($expected1, $result1);

        $result2 = $this->lineItemFactory->createWithData('UPS', 1, 1000, 1000, 1000, 200, 1200, null, null, '20%');
        $expected2 = new LineItem(
            'UPS',
            1,
            1000,
            1000,
            1000,
            200,
            1200,
            null,
            null,
            '20%',
        );

        self::assertEquals($expected2, $result2);
    }
}
