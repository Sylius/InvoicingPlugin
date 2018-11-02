<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Event;

final class OrderPaymentPaid
{
    /** @var string */
    private $orderNumber;

    /** @var \DateTimeInterface */
    private $date;

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
