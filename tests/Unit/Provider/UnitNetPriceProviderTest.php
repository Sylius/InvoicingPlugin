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

namespace Tests\Sylius\InvoicingPlugin\Unit\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\InvoicingPlugin\Provider\UnitNetPriceProvider;
use Sylius\InvoicingPlugin\Provider\UnitNetPriceProviderInterface;

final class UnitNetPriceProviderTest extends TestCase
{
    private UnitNetPriceProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new UnitNetPriceProvider();
    }

    #[Test]
    public function it_implements_unit_net_price_provider_interface(): void
    {
        self::assertInstanceOf(UnitNetPriceProviderInterface::class, $this->provider);
    }

    #[Test]
    public function it_provides_net_price_for_unit_with_taxes_included_in_price(): void
    {
        $unit = $this->createMock(OrderItemUnitInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $taxAdjustment = $this->createMock(AdjustmentInterface::class);

        $unit->method('getOrderItem')->willReturn($orderItem);
        $orderItem->method('getUnitPrice')->willReturn(1000);

        $unit
            ->method('getAdjustments')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment]));

        $taxAdjustment->method('isNeutral')->willReturn(true);
        $taxAdjustment->method('getAmount')->willReturn(200);

        $result = $this->provider->getUnitNetPrice($unit);

        self::assertSame(800, $result);
    }

    #[Test]
    public function it_provides_net_price_for_unit_with_taxes_excluded_of_price(): void
    {
        $unit = $this->createMock(OrderItemUnitInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $taxAdjustment = $this->createMock(AdjustmentInterface::class);

        $unit->method('getOrderItem')->willReturn($orderItem);
        $orderItem->method('getUnitPrice')->willReturn(1000);

        $unit
            ->method('getAdjustments')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment]));

        $taxAdjustment->method('isNeutral')->willReturn(false);
        $taxAdjustment->method('getAmount')->willReturn(200);

        $result = $this->provider->getUnitNetPrice($unit);

        self::assertSame(1000, $result);
    }
}
