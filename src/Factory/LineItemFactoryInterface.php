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

namespace Sylius\InvoicingPlugin\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;

interface LineItemFactoryInterface extends FactoryInterface
{
    public function createWithData(
        string $name,
        int $quantity,
        int $unitNetPrice,
        int $discountedUnitNetPrice,
        int $subtotal,
        int $taxTotal,
        int $total,
        ?string $variantName = null,
        ?string $variantCode = null,
        ?string $taxRate = null,
    ): LineItemInterface;
}
