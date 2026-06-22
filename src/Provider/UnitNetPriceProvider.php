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

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxRate;

final class UnitNetPriceProvider implements UnitNetPriceProviderInterface
{
    public function __construct(
        private ?CalculatorInterface $taxCalculator = null,
    ) {
        if (null === $taxCalculator) {
            trigger_deprecation(
                'sylius/invoicing-plugin',
                '2.2',
                'Not passing a "%s" instance to "%s" is deprecated and it will be required in 3.0.',
                CalculatorInterface::class,
                self::class,
            );
        }
    }

    public function getUnitNetPrice(OrderItemUnitInterface $orderItemUnit): int
    {
        $orderItem = $orderItemUnit->getOrderItem();
        $unitPrice = $orderItem->getUnitPrice();
        /** @var AdjustmentInterface $adjustment */
        foreach ($orderItemUnit->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT) as $adjustment) {
            if ($adjustment->isNeutral()) {
                /** @var float $taxRateAmount */
                $taxRateAmount = $adjustment->getDetails()['taxRateAmount'];

                $unitPrice -= $this->calculateIncludedTax($unitPrice, $taxRateAmount);
            }
        }

        return $unitPrice;
    }

    private function calculateIncludedTax(int $unitPrice, float $taxRateAmount): int
    {
        if (null !== $this->taxCalculator) {
            $taxRate = new TaxRate();
            $taxRate->setAmount($taxRateAmount);
            $taxRate->setIncludedInPrice(true);

            return (int) round($this->taxCalculator->calculate($unitPrice, $taxRate));
        }

        return (int) round($unitPrice - ($unitPrice / (1 + $taxRateAmount)));
    }
}
