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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\InvoicingPlugin\Entity\LineItem;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProviderInterface;
use Webmozart\Assert\Assert;

final class OrderItemUnitsToLineItemsConverter implements LineItemsConverterInterface
{
    /** @var TaxRatePercentageProviderInterface */
    private $taxRatePercentageProvider;

    public function __construct(TaxRatePercentageProviderInterface $taxRatePercentageProvider)
    {
        $this->taxRatePercentageProvider = $taxRatePercentageProvider;
    }

    public function convert(OrderInterface $order): Collection
    {
        $lineItems = [];

        /** @var OrderItemUnitInterface $unit */
        foreach ($order->getItemUnits() as $unit) {
            $lineItems = $this->addLineItem($this->convertOrderItemUnitToLineItem($unit), $lineItems);
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
            $this->taxRatePercentageProvider->provideFromAdjustable($unit)
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
