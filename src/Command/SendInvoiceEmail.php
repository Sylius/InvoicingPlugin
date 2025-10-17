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

namespace Sylius\InvoicingPlugin\Command;

final readonly class SendInvoiceEmail
{
    public function __construct(
        private string $orderNumber,
        private int $attempt = 0,
    ) {
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function attempt(): int
    {
        return $this->attempt;
    }
}
