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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Factory\LineItemFactoryInterface;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProviderInterface;
use Sylius\InvoicingPlugin\Provider\UnitNetPriceProviderInterface;
use Webmozart\Assert\Assert;

final class OrderItemUnitsToLineItemsConverter implements LineItemsConverterInterface
{
    public function __construct(
        private readonly TaxRatePercentageProviderInterface $taxRatePercentageProvider,
        private readonly LineItemFactoryInterface $lineItemFactory,
        private readonly UnitNetPriceProviderInterface $unitNetPriceProvider,
    ) {
    }

    public function convert(OrderInterface $order): array
    {
        $lineItems = [];

        /** @var OrderItemUnitInterface $unit */
        foreach ($order->getItemUnits() as $unit) {
            $lineItems = $this->addLineItem($this->convertOrderItemUnitToLineItem($unit), $lineItems);
        }

        return $lineItems;
    }

    private function convertOrderItemUnitToLineItem(OrderItemUnitInterface $unit): LineItemInterface
    {
        /** @var OrderItemInterface $item */
        $item = $unit->getOrderItem();

        $grossValue = $unit->getTotal();
        $taxAmount = $unit->getTaxTotal();
        $unitPrice = $this->unitNetPriceProvider->getUnitNetPrice($unit);
        $discountedUnitNetPrice = $grossValue - $taxAmount;

        /** @var string|null $productName */
        $productName = $item->getProductName();
        Assert::notNull($productName);

        $variant = $item->getVariant();

        return $this->lineItemFactory->createWithData(
            $productName,
            1,
            $unitPrice,
            $discountedUnitNetPrice,
            $discountedUnitNetPrice,
            $taxAmount,
            $grossValue,
            $item->getVariantName(),
            $variant !== null ? $variant->getCode() : null,
            $this->taxRatePercentageProvider->provideFromAdjustable($unit),
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
}
