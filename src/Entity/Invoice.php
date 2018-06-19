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

    public function __construct(string $invoiceId, string $orderId)
    {
        $this->invoiceId = $invoiceId;
        $this->orderId = $orderId;
    }

    public function invoiceId(): string
    {
        return $this->invoiceId;
    }

    public function orderId(): string
    {
        return $this->orderId;
    }
}
