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
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\InvoicingPlugin\Exception\MoreThanOneTaxAdjustment;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProvider;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProviderInterface;

final class TaxRatePercentageProviderTest extends TestCase
{
    private TaxRatePercentageProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new TaxRatePercentageProvider();
    }

    #[Test]
    public function it_implements_tax_rate_percentage_provider_interface(): void
    {
        self::assertInstanceOf(TaxRatePercentageProviderInterface::class, $this->provider);
    }

    #[Test]
    public function it_provides_a_tax_rate_from_adjustable_entity(): void
    {
        $orderItemUnit = $this->createMock(OrderItemUnitInterface::class);
        $taxAdjustment = $this->createMock(AdjustmentInterface::class);

        $orderItemUnit
            ->method('getAdjustments')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment]));

        $taxAdjustment->method('getDetails')->willReturn(['taxRateAmount' => 0.2]);

        $result = $this->provider->provideFromAdjustable($orderItemUnit);

        self::assertSame('20%', $result);
    }

    #[Test]
    public function it_returns_null_if_there_is_no_tax_adjustment_on_adjustable_entity(): void
    {
        $orderItemUnit = $this->createMock(OrderItemUnitInterface::class);

        $orderItemUnit
            ->method('getAdjustments')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([]));

        $result = $this->provider->provideFromAdjustable($orderItemUnit);

        self::assertNull($result);
    }

    #[Test]
    public function it_throws_an_exception_if_there_is_no_tax_rate_amount_in_details_of_adjustment(): void
    {
        $orderItemUnit = $this->createMock(OrderItemUnitInterface::class);
        $taxAdjustment = $this->createMock(AdjustmentInterface::class);

        $orderItemUnit
            ->method('getAdjustments')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment]));

        $taxAdjustment->method('getDetails')->willReturn([]);

        $this->expectException(\InvalidArgumentException::class);

        $this->provider->provideFromAdjustable($orderItemUnit);
    }

    #[Test]
    public function it_throws_an_exception_if_order_item_unit_has_more_adjustments_than_one(): void
    {
        $orderItemUnit = $this->createMock(OrderItemUnitInterface::class);
        $firstTaxAdjustment = $this->createMock(AdjustmentInterface::class);
        $secondTaxAdjustment = $this->createMock(AdjustmentInterface::class);

        $orderItemUnit
            ->method('getAdjustments')
            ->with(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$firstTaxAdjustment, $secondTaxAdjustment]));

        $this->expectException(MoreThanOneTaxAdjustment::class);

        $this->provider->provideFromAdjustable($orderItemUnit);
    }

    #[Test]
    public function it_provides_a_tax_rate_from_adjustment(): void
    {
        $taxAdjustment = $this->createMock(AdjustmentInterface::class);

        $taxAdjustment->method('getDetails')->willReturn(['taxRateAmount' => 0.2]);

        $result = $this->provider->provideFromAdjustment($taxAdjustment);

        self::assertSame('20%', $result);
    }
}
