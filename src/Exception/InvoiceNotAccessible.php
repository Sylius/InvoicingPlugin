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

namespace Sylius\InvoicingPlugin\Exception;

final class InvoiceNotAccessible extends \InvalidArgumentException
{
    public static function withUserId(string $invoiceId, int $userId): self
    {
        return new self(sprintf('Invoice with id "%s" is not accessible for user with id "%s"', $invoiceId, $userId));
    }
}
