<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Event;

use Prooph\Common\Messaging\DomainEvent;
use Prooph\Common\Messaging\PayloadTrait;
use Sylius\Component\Core\Model\OrderInterface;

final class OrderPlaced extends DomainEvent
{
    use PayloadTrait;

    public function __construct(OrderInterface $order, \DateTimeInterface $date)
    {
        $this->init();
        $this->setPayload(['order' => $order, 'date' => $date]);
    }

    public function order(): OrderInterface
    {
        return $this->payload()['order'];
    }

    public function date(): \DateTimeInterface
    {
        return $this->payload()['date'];
    }
}
