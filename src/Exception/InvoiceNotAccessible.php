<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Exception;

final class InvoiceNotAccessible extends \InvalidArgumentException
{
    public static function withUserId(string $invoiceId, int $userId): self
    {
        return new self(sprintf('Invoice with id "%s" is not accessible for user with id "%s"', $invoiceId, $userId));
    }
}
