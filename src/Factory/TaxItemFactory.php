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

namespace Sylius\InvoicingPlugin\Factory;

use Sylius\Component\Resource\Exception\UnsupportedMethodException;
use Sylius\InvoicingPlugin\Entity\TaxItem;
use Sylius\InvoicingPlugin\Entity\TaxItemInterface;

final class TaxItemFactory implements TaxItemFactoryInterface
{
    private string $className;

    public function __construct(string $className)
    {
        if (!is_a($className, TaxItem::class, true)) {
            throw new \DomainException(sprintf(
                'This factory requires %s or its descend to be used as tax item resource',
                TaxItem::class
            ));
        }

        $this->className = $className;
    }

    public function createNew()
    {
        throw new UnsupportedMethodException('createNew');
    }

    public function createWithData(string $label, int $amount): TaxItemInterface
    {
        return new $this->className(
            $label,
            $amount
        );
    }
}
