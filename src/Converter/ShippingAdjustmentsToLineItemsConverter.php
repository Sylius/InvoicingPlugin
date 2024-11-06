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

namespace Sylius\InvoicingPlugin\Converter;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Exception\MoreThanOneTaxAdjustment;
use Sylius\InvoicingPlugin\Factory\LineItemFactoryInterface;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProviderInterface;
use Webmozart\Assert\Assert;

final class ShippingAdjustmentsToLineItemsConverter implements LineItemsConverterInterface
{
    public function __construct(
        private readonly TaxRatePercentageProviderInterface $taxRatePercentageProvider,
        private readonly LineItemFactoryInterface $lineItemFactory,
    ) {
    }

    public function convert(OrderInterface $order): array
    {
        $lineItems = [];

        /** @var AdjustmentInterface $shippingAdjustment */
        foreach ($order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT) as $shippingAdjustment) {
            $lineItems[] = $this->convertShippingAdjustmentToLineItem($shippingAdjustment, $order);
        }

        return $lineItems;
    }

    private function convertShippingAdjustmentToLineItem(
        AdjustmentInterface $shippingAdjustment,
        OrderInterface $order,
    ): LineItemInterface {
        /** @var ShipmentInterface|null $shipment */
        $shipment = $shippingAdjustment->getShipment();
        Assert::notNull($shipment);
        Assert::isInstanceOf($shipment, AdjustableInterface::class);

        $grossValue = $shipment->getAdjustmentsTotal();
        $taxAdjustment = $this->getShipmentTaxAdjustment($shipment);
        $taxAmount = $taxAdjustment !== null ? $taxAdjustment->getAmount() : 0;
        $unitNetPrice = $grossValue - $taxAmount - $shipment->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
        $discountedUnitNetPrice = $grossValue - $taxAmount;

        return $this->lineItemFactory->createWithData(
            $shippingAdjustment->getLabel(),
            1,
            $unitNetPrice,
            $discountedUnitNetPrice,
            $discountedUnitNetPrice,
            $taxAmount,
            $grossValue,
            null,
            null,
            $this->taxRatePercentageProvider->provideFromAdjustable($shipment),
        );
    }

    private function getShipmentTaxAdjustment(ShipmentInterface $shipment): ?AdjustmentInterface
    {
        $taxAdjustments = $shipment->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);
        if ($taxAdjustments->isEmpty()) {
            return null;
        }

        if ($taxAdjustments->count() > 1) {
            throw MoreThanOneTaxAdjustment::occur();
        }

        /** @var AdjustmentInterface $taxAdjustment */
        $taxAdjustment = $taxAdjustments->first();

        return $taxAdjustment;
    }
}
