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
use Sylius\InvoicingPlugin\Provider\ItemNetPricesProviderInterface;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProviderInterface;
use Webmozart\Assert\Assert;

final class OrderItemsToLineItemsConverter implements LineItemsConverterInterface
{
    public function __construct(
        private readonly TaxRatePercentageProviderInterface $taxRatePercentageProvider,
        private readonly LineItemFactoryInterface $lineItemFactory,
        private readonly ItemNetPricesProviderInterface $unitNetPriceProvider,
    ) {
    }

    public function convert(OrderInterface $order): array
    {
        $lineItems = [];

        /** @var OrderItemInterface $item */
        foreach ($order->getItems() as $item) {
            foreach ($this->convertOrderItemToLineItems($item) as $lineItem) {
                $lineItems = $this->addLineItem($lineItem, $lineItems);
            }
        }

        return $lineItems;
    }

    private function convertOrderItemToLineItems(OrderItemInterface $item): array
    {
        $lineItems = [];
        $units = $item->getUnits()->getValues();
        $unitNetPrices = $this->unitNetPriceProvider->getItemNetPrices($item);

        /** @var OrderItemUnitInterface $unit */
        foreach ($units as $index => $unit) {
            $lineItems = $this->addLineItem($this->convertOrderItemUnitToLineItem($unit, (int) $unitNetPrices[$index]), $lineItems);
        }

        return $lineItems;
    }

    private function convertOrderItemUnitToLineItem(OrderItemUnitInterface $unit, int $unitNetPrice): LineItemInterface
    {
        /** @var OrderItemInterface $item */
        $item = $unit->getOrderItem();

        $grossValue = $unit->getTotal();
        $taxAmount = $unit->getTaxTotal();
        $discountedUnitNetPrice = $grossValue - $taxAmount;

        /** @var string|null $productName */
        $productName = $item->getProductName();
        Assert::notNull($productName);

        $variant = $item->getVariant();

        return $this->lineItemFactory->createWithData(
            $productName,
            1,
            $unitNetPrice,
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
