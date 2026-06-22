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
use Sylius\Component\Taxation\Calculator\DefaultCalculator;
use Sylius\InvoicingPlugin\Provider\UnitNetPriceProvider;
use Sylius\InvoicingPlugin\Provider\UnitNetPriceProviderInterface;

final class UnitNetPriceProviderTest extends TestCase
{
    private UnitNetPriceProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new UnitNetPriceProvider(new DefaultCalculator());
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
        $taxAdjustment->method('getDetails')->willReturn(['taxRateAmount' => 0.25]);

        $result = $this->provider->getUnitNetPrice($unit);

        self::assertSame(800, $result);
    }

    #[Test]
    public function it_provides_net_price_for_unit_with_taxes_included_in_price_and_promotion_applied(): void
    {
        $unit = $this->createMock(OrderItemUnitInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $taxAdjustment = $this->createMock(AdjustmentInterface::class);

        $unit->method('getOrderItem')->willReturn($orderItem);
        $orderItem->method('getUnitPrice')->willReturn(12000);

        $unit
            ->method('getAdjustments')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment]));

        // Neutral tax adjustment: amount is calculated on discounted price (post-promotion),
        // so using getAmount() directly would give a wrong result.
        $taxAdjustment->method('isNeutral')->willReturn(true);
        $taxAdjustment->method('getDetails')->willReturn(['taxRateAmount' => 0.20]);

        $result = $this->provider->getUnitNetPrice($unit);

        self::assertSame(10000, $result);
    }

    #[Test]
    public function it_triggers_a_deprecation_and_calculates_the_tax_manually_when_no_calculator_is_passed(): void
    {
        $deprecation = null;
        set_error_handler(static function (int $errno, string $errstr) use (&$deprecation): bool {
            $deprecation = $errstr;

            return true;
        }, \E_USER_DEPRECATED);

        try {
            $provider = new UnitNetPriceProvider();
        } finally {
            restore_error_handler();
        }

        self::assertNotNull($deprecation);
        self::assertStringContainsString('will be required in 3.0', $deprecation);

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
        $taxAdjustment->method('getDetails')->willReturn(['taxRateAmount' => 0.25]);

        self::assertSame(800, $provider->getUnitNetPrice($unit));
    }

    #[Test]
    public function it_calculates_the_tax_manually_on_the_full_unit_price_when_no_calculator_is_passed_and_a_promotion_is_applied(): void
    {
        $deprecation = null;
        set_error_handler(static function (int $errno, string $errstr) use (&$deprecation): bool {
            $deprecation = $errstr;

            return true;
        }, \E_USER_DEPRECATED);

        try {
            $provider = new UnitNetPriceProvider();
        } finally {
            restore_error_handler();
        }

        $unit = $this->createMock(OrderItemUnitInterface::class);
        $orderItem = $this->createMock(OrderItemInterface::class);
        $taxAdjustment = $this->createMock(AdjustmentInterface::class);

        $unit->method('getOrderItem')->willReturn($orderItem);
        $orderItem->method('getUnitPrice')->willReturn(12000);

        $unit
            ->method('getAdjustments')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment]));

        // The neutral tax adjustment amount is calculated on the discounted (post-promotion)
        // price, so subtracting getAmount() directly would diverge from the real net price.
        // The tax must be recalculated on the full unit price instead.
        $taxAdjustment->method('isNeutral')->willReturn(true);
        $taxAdjustment->method('getDetails')->willReturn(['taxRateAmount' => 0.20]);

        self::assertSame(10000, $provider->getUnitNetPrice($unit));
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
