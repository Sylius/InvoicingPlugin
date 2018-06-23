<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

/** @final */
class LineItem implements LineItemInterface, ResourceInterface
{
    /** @var string */
    private $id;

    /** @var InvoiceInterface */
    private $invoice;

    /** @var string */
    private $name;

    /** @var string|null */
    private $variantName;

    /** @var string|null */
    private $variantCode;

    /** @var int */
    private $quantity;

    /** @var int */
    private $unitPrice;

    /** @var int */
    private $subtotal;

    /** @var int */
    private $taxTotal;

    /** @var int */
    private $total;

    public function __construct(
        string $name,
        int $quantity,
        int $unitPrice,
        int $subtotal,
        int $taxTotal,
        int $total,
        ?string $variantName = null,
        ?string $variantCode = null
    ) {
        $this->name = $name;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->subtotal = $subtotal;
        $this->taxTotal = $taxTotal;
        $this->total = $total;
        $this->variantName = $variantName;
        $this->variantCode = $variantCode;
    }

    public function getId(): string
    {
        return $this->id();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function invoice(): InvoiceInterface
    {
        return $this->invoice;
    }

    public function setInvoice(InvoiceInterface $invoice): void
    {
        $this->invoice = $invoice;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function variantName(): ?string
    {
        return $this->variantName;
    }

    public function variantCode(): ?string
    {
        return $this->variantCode;
    }

    public function quantity(): int
    {
        return $this->quantity;
    }

    public function unitPrice(): int
    {
        return $this->unitPrice;
    }

    public function subtotal(): int
    {
        return $this->subtotal;
    }

    public function taxTotal(): int
    {
        return $this->taxTotal;
    }

    public function total(): int
    {
        return $this->total;
    }
}
