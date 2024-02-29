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

namespace Sylius\InvoicingPlugin\Exception;

final class InvoiceAlreadyGenerated extends \DomainException
{
    public static function withOrderNumber(string $orderNumber): self
    {
        return new self(sprintf('An invoice for order with number %s was already generated', $orderNumber));
    }
}
