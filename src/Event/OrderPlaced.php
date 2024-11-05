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

namespace Sylius\InvoicingPlugin\Event;

final class OrderPlaced
{
    private readonly \DateTimeInterface $date;

    public function __construct(
        private readonly string $orderNumber,
        \DateTimeInterface $date,
    ) {
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
