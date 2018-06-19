<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

/**
 * @final
 */
class Invoice
{
    /** @var string */
    private $id;

    /** @var string */
    private $orderId;

    /** @var \DateTimeInterface */
    private $issuedAt;

    public function __construct(string $id, string $orderId, \DateTimeInterface $issuedAt)
    {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->issuedAt = clone $issuedAt;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function orderId(): string
    {
        return $this->orderId;
    }

    public function issuedAt(): \DateTimeInterface
    {
        return clone $this->issuedAt;
    }
}
