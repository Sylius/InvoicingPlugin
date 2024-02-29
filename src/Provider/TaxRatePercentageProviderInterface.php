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
use Sylius\Component\Order\Model\AdjustableInterface;

interface TaxRatePercentageProviderInterface
{
    public function provideFromAdjustable(AdjustableInterface $adjustable): ?string;

    public function provideFromAdjustment(AdjustmentInterface $adjustment): ?string;
}
