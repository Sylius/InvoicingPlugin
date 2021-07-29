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
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\InvoicingPlugin\Entity\LineItem;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Exception\MoreThanOneTaxAdjustment;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProviderInterface;
use Webmozart\Assert\Assert;

final class ShippingAdjustmentsToLineItemsConverter implements LineItemsConverterInterface
{
    /** @var TaxRatePercentageProviderInterface */
    private $taxRatePercentageProvider;

    public function __construct(TaxRatePercentageProviderInterface $taxRatePercentageProvider)
    {
        $this->taxRatePercentageProvider = $taxRatePercentageProvider;
    }

    public function convert(OrderInterface $order): Collection
    {
        $lineItems = new ArrayCollection();

        /** @var AdjustmentInterface $shippingAdjustment */
        foreach ($order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT) as $shippingAdjustment) {
            $lineItems->add($this->convertShippingAdjustmentToLineItem($shippingAdjustment));
        }

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
            $this->taxRatePercentageProvider->provideFromAdjustable($shipment)
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
