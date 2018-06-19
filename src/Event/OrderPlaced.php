<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Event;

use Prooph\Common\Messaging\DomainEvent;
use Prooph\Common\Messaging\PayloadTrait;

final class OrderPlaced extends DomainEvent
{
    use PayloadTrait;

    public function __construct(string $orderNumber, \DateTimeInterface $date)
    {
        $this->init();
        $this->setPayload(['order_number' => $orderNumber, 'date' => $date]);
    }

    public function orderNumber(): string
    {
        return $this->payload()['order_number'];
    }

    public function date(): \DateTimeInterface
    {
        return $this->payload()['date'];
    }
}
