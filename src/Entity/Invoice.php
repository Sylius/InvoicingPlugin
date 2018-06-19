<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

/**
 * @final
 */
class Invoice
{
    /** @var string */
    private $invoiceId;

    /** @var string */
    private $orderId;

    /** @var \DateTimeInterface */
    private $issuedAt;

    public function __construct(string $invoiceId, string $orderId, \DateTimeInterface $issuedAt)
    {
        $this->invoiceId = $invoiceId;
        $this->orderId = $orderId;
        $this->issuedAt = clone $issuedAt;
    }

    public function invoiceId(): string
    {
        return $this->invoiceId;
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
