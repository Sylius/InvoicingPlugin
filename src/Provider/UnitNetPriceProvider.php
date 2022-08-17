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

namespace Sylius\InvoicingPlugin\Provider;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;

final class UnitNetPriceProvider implements UnitNetPriceProviderInterface
{
    public function getUnitNetPrice(OrderItemUnitInterface $orderItemUnit): int
    {
        $orderItem = $orderItemUnit->getOrderItem();
        $unitPrice = $orderItem->getUnitPrice();
        /** @var AdjustmentInterface $adjustment */
        foreach ($orderItemUnit->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT) as $adjustment) {
            if ($adjustment->isNeutral()) {
                $unitPrice -= $adjustment->getAmount();
            }
        }

        return $unitPrice;
    }
}
