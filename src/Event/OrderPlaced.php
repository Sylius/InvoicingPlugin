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

final class OrderPlaced
{
    private int $orderId;

    private \DateTimeInterface $date;

    public function __construct(int $orderId, \DateTimeInterface $date)
    {
        $this->orderId = $orderId;
        $this->date = clone $date;
    }

    public function orderId(): int
    {
        return $this->orderId;
    }

    public function date(): \DateTimeInterface
    {
        return clone $this->date;
    }
}
