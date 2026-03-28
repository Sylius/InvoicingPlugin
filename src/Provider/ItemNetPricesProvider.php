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

namespace Sylius\InvoicingPlugin\Provider;

use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;

final class ItemNetPricesProvider implements ItemNetPricesProviderInterface
{
    public function __construct(
        private IntegerDistributorInterface $distributor,
    ) {
    }

    public function getItemNetPrices(OrderItemInterface $orderItem): array
    {
        /** @var OrderItemUnitInterface|null $orderItemUnit */
        $orderItemUnit = $orderItem->getUnits()->first();

        if (null === $orderItemUnit) {
            return [];
        }

        $taxRate = $this->getTaxRate($orderItemUnit);
        $grossTotal = $orderItem->getQuantity() * $orderItem->getUnitPrice();

        $itemNetPrice = ($grossTotal / (100 + ($taxRate))) * 100;

        return array_reverse($this->distributor->distribute(round($itemNetPrice, 1), $orderItem->getQuantity()));
    }

    private function getTaxRate(OrderItemUnitInterface $orderItemUnit): int
    {
        $taxRate = 0;

        /** @var AdjustmentInterface $adjustment */
        foreach ($orderItemUnit->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT) as $adjustment) {
            if (!$adjustment->isNeutral()) {
                continue;
            }

            try {
                $details = $adjustment->getDetails();
                if (is_array($details) && array_key_exists('taxRateAmount', $details) && is_numeric($details['taxRateAmount'])) {
                    $taxRate = $details['taxRateAmount'] * 100;
                }
            } catch (\Throwable $e) {
                throw new \RuntimeException('Tax rate amount is not valid', 0, $e);
            }
        }

        return (int) round($taxRate);
    }
}
