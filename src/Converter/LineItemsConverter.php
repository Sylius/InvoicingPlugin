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
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\InvoicingPlugin\Entity\LineItem;
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
        $orderItems = $order->getItems();

        $lineItems = new ArrayCollection();

        $this->addLineItemForShipment($lineItems, $order);

        /** @var OrderItemInterface $orderItem */
        foreach ($orderItems as $orderItem) {
            $variant = $orderItem->getVariant();

            $lineItems->add(new LineItem(
                $orderItem->getProductName(),
                $orderItem->getQuantity(),
                (int) ($orderItem->getTotal() / $orderItem->getQuantity() - $orderItem->getTaxTotal() / $orderItem->getQuantity()),
                $orderItem->getTotal() - $orderItem->getTaxTotal(),
                $orderItem->getTaxTotal(),
                $orderItem->getTotal(),
                $orderItem->getVariantName(),
                $variant !== null ? $variant->getCode() : null,
                $this->taxRateProvider->provide($orderItem)
            ));
        }

        return $lineItems;
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

    private function getShipmentPromotionsAdjustmentAmount(ShipmentInterface $shipment): int
    {
        $promotionAdjustments = $shipment->getAdjustments(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);

        if ($promotionAdjustments->isEmpty()) {
            return 0;
        }

        $promotionAdjustmentsAmount = 0;

        foreach ($promotionAdjustments as $promotionAdjustment) {
            $promotionAdjustmentsAmount = $promotionAdjustmentsAmount + $promotionAdjustment->getAmount();
        }

        return $promotionAdjustmentsAmount;
    }

    private function addLineItemForShipment(ArrayCollection $lineItems, OrderInterface $order): void
    {
        /** @var ShipmentInterface $shipment */
        $shipment = null;

        if (!$order->getShipments()->isEmpty()) {
            $shipment = $order->getShipments()->first();
        }

        Assert::notNull($shipment);
        Assert::isInstanceOf($shipment, AdjustableInterface::class);

        $promotionAdjustmentsAmount = $this->getShipmentPromotionsAdjustmentAmount($shipment);

        /** @var AdjustmentInterface $shippingAdjustment */
        $shippingAdjustment = $shipment->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)->first();

        $taxAdjustment = $this->getShipmentTaxAdjustment($shipment);
        $taxAdjustmentAmount = $taxAdjustment !== null ? $taxAdjustment->getAmount() : 0;

        /** @var string|null $label */
        $label = $shippingAdjustment->getLabel();
        Assert::notNull($label);

        $lineItems->add(new LineItem(
            $label,
            1,
            $promotionAdjustmentsAmount + $shippingAdjustment->getAmount(),
            $promotionAdjustmentsAmount + $shippingAdjustment->getAmount(),
            $taxAdjustmentAmount,
            $promotionAdjustmentsAmount + $shippingAdjustment->getAmount() + $taxAdjustmentAmount,
            null,
            null,
            $this->taxRateProvider->provide($shipment)
        ));
    }
}
