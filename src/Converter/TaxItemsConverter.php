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
use Sylius\InvoicingPlugin\Entity\TaxItem;
use Sylius\InvoicingPlugin\Provider\TaxRateProviderInterface;
use Webmozart\Assert\Assert;

final class TaxItemsConverter implements TaxItemsConverterInterface
{
    /** @var TaxRateProviderInterface */
    private $taxRateProvider;

    public function __construct(TaxRateProviderInterface $taxRateProvider)
    {
        $this->taxRateProvider = $taxRateProvider;
    }

    public function convert(OrderInterface $order): Collection
    {
        $temporaryTaxItems = [];
        $taxItems = new ArrayCollection();

        $taxAdjustments = $order->getAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT);
        foreach ($taxAdjustments as $taxAdjustment) {
            $taxRateLabel = $this->taxRateProvider->provideFromAdjustment($taxAdjustment);

            Assert::notNull($taxRateLabel);

            if (array_key_exists($taxRateLabel, $temporaryTaxItems)) {
                $temporaryTaxItems[$taxRateLabel] += $taxAdjustment->getAmount();

                continue;
            }

            $temporaryTaxItems[$taxRateLabel] = $taxAdjustment->getAmount();
        }

        foreach ($temporaryTaxItems as $label => $amount) {
            $taxItems->add(new TaxItem($label, $amount));
        }

        return $taxItems;
    }
}
