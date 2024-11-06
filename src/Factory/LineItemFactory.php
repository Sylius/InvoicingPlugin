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

use Sylius\Component\Resource\Exception\UnsupportedMethodException;
use Sylius\InvoicingPlugin\Entity\LineItem;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Webmozart\Assert\Assert;

final class LineItemFactory implements LineItemFactoryInterface
{
    public function __construct(private readonly string $className)
    {
        if (!is_a($className, LineItem::class, true)) {
            throw new \DomainException(sprintf(
                'This factory requires %s or its descend to be used as line item resource',
                LineItem::class,
            ));
        }
    }

    public function createNew()
    {
        throw new UnsupportedMethodException('createNew');
    }

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
    ): LineItemInterface {
        /** @var LineItemInterface $lineItem */
        $lineItem = new $this->className(
            $name,
            $quantity,
            $unitNetPrice,
            $discountedUnitNetPrice,
            $subtotal,
            $taxTotal,
            $total,
            $variantName,
            $variantCode,
            $taxRate,
        );

        Assert::isInstanceOf($lineItem, LineItemInterface::class);

        return $lineItem;
    }
}
