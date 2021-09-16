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

namespace Sylius\InvoicingPlugin\Command;

final class SendInvoiceEmail
{
    private string $orderNumber;

    public function __construct(string $orderNumber)
    {
        $this->orderNumber = $orderNumber;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }
}
