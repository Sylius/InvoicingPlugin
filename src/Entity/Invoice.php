<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

/**
 * @final
 */
class Invoice implements InvoiceInterface, ResourceInterface
{
    /** @var string */
    private $id;

    /** @var string */
    private $orderNumber;

    /** @var \DateTimeInterface */
    private $issuedAt;

    public function __construct(string $id, string $orderNumber, \DateTimeInterface $issuedAt)
    {
        $this->id = $id;
        $this->orderNumber = $orderNumber;
        $this->issuedAt = clone $issuedAt;
    }

    public function getId(): string
    {
        return $this->id();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function issuedAt(): \DateTimeInterface
    {
        return clone $this->issuedAt;
    }
}
