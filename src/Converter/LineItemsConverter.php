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

namespace Sylius\InvoicingPlugin\Converter;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\InvoicingPlugin\Entity\LineItem;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Exception\MoreThanOneTaxAdjustment;
use Sylius\InvoicingPlugin\Provider\TaxRateProviderInterface;
use Webmozart\Assert\Assert;

final class LineItemsConverter implements LineItemsConverterInterface
{
    /** @var TaxRateProviderInterface */
    private $taxRateProvider;

    public function __construct(TaxRateProviderInterface $taxRateProvider)
    {
        $this->taxRateProvider = $taxRateProvider;
    }

    public function convert(OrderInterface $order): Collection
    {
        $lineItems = [];

        /** @var OrderItemUnitInterface $unit */
        foreach ($order->getItemUnits() as $unit) {
            $lineItems = $this->addLineItem($this->convertOrderItemUnitToLineItem($unit), $lineItems);
        }

        /** @var AdjustmentInterface $shippingAdjustment */
        foreach ($order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT) as $shippingAdjustment) {
            $lineItems[] = $this->convertShippingAdjustmentToLineItem($shippingAdjustment);
        }

        return new ArrayCollection($lineItems);
    }

    private function convertOrderItemUnitToLineItem(OrderItemUnitInterface $unit): LineItemInterface
    {
        /** @var OrderItemInterface $item */
        $item = $unit->getOrderItem();

        $grossValue = $unit->getTotal();
        $taxAmount = $unit->getTaxTotal();
        $netValue = $grossValue - $taxAmount;

        /** @var string|null $productName */
        $productName = $item->getProductName();
        Assert::notNull($productName);

        $variant = $item->getVariant();

        return new LineItem(
            $productName,
            1,
            $netValue,
            $netValue,
            $taxAmount,
            $grossValue,
            $item->getVariantName(),
            $variant !== null ? $variant->getCode() : null,
            $this->taxRateProvider->provide($unit)
        );
    }

    /**
     * @param LineItemInterface[] $lineItems
     *
     * @return LineItemInterface[]
     */
    private function addLineItem(LineItemInterface $newLineItem, array $lineItems): array
    {
        foreach ($lineItems as $lineItem) {
            if ($lineItem->compare($newLineItem)) {
                $lineItem->merge($newLineItem);

                return $lineItems;
            }
        }

        $lineItems[] = $newLineItem;

        return $lineItems;
    }

    private function convertShippingAdjustmentToLineItem(AdjustmentInterface $shippingAdjustment): LineItemInterface
    {
        /** @var ShipmentInterface|null $shipment */
        $shipment = $shippingAdjustment->getShipment();
        Assert::notNull($shipment);
        Assert::isInstanceOf($shipment, AdjustableInterface::class);

        $grossValue = $shipment->getAdjustmentsTotal();
        $taxAdjustment = $this->getShipmentTaxAdjustment($shipment);
        $taxAmount = $taxAdjustment !== null ? $taxAdjustment->getAmount() : 0;
        $netValue = $grossValue - $taxAmount;

        return new LineItem(
            $shippingAdjustment->getLabel(),
            1,
            $netValue,
            $netValue,
            $taxAmount,
            $grossValue,
            null,
            null,
            $this->taxRateProvider->provide($shipment)
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
