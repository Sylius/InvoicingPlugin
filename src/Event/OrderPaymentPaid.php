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

namespace Sylius\InvoicingPlugin\Event;

final class OrderPaymentPaid
{
    private string $orderNumber;

    private \DateTimeInterface $date;

    public function __construct(string $orderNumber, \DateTimeInterface $date)
    {
        $this->orderNumber = $orderNumber;
        $this->date = clone $date;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function date(): \DateTimeInterface
    {
        return clone $this->date;
    }
}
