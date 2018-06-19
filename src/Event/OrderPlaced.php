<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Event;

final class OrderPlaced
{
    /** @var string */
    private $orderId;

    /** @var \DateTimeInterface */
    private $date;

    public function __construct(string $orderId, \DateTimeInterface $date)
    {
        $this->orderId = $orderId;
        $this->date = $date;
    }

    public function orderId(): string
    {
        return $this->orderId;
    }

    public function date(): \DateTimeInterface
    {
        return $this->date;
    }
}
