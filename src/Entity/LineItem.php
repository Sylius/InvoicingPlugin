<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

/** @final */
class LineItem implements LineItemInterface, ResourceInterface
{
    public const TYPE_ITEM = 'item';
    public const TYPE_SHIPPING = 'shipping';

    /** @var string */
    private $id;

    /** @var InvoiceInterface */
    private $invoice;

    /** @var string */
    protected $type;

    /** @var string */
    protected $name;

    /** @var string|null */
    protected $variantName;

    /** @var string|null */
    protected $variantCode;

    /** @var array|null */
    protected $variantOptions;

    /** @var string|null */
    protected $itemNumber;

    /** @var int */
    protected $quantity;

    /** @var int */
    protected $unitPrice;

    /** @var int */
    protected $subtotal;

    /** @var int */
    protected $promotionTotal;

    /** @var int */
    protected $taxTotal;

    /** @var int */
    protected $total;

    public function __construct(
        string $type,
        string $name,
        int $quantity,
        int $unitPrice,
        int $subtotal,
        int $promotionTotal,
        int $taxTotal,
        int $total,
        ?string $variantName = null,
        ?string $variantCode = null,
        array $variantOptions = [],
        ?string $itemNumber = null
    ) {
        $this->type = $type;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->subtotal = $subtotal;
        $this->promotionTotal = $promotionTotal;
        $this->taxTotal = $taxTotal;
        $this->total = $total;
        $this->variantName = $variantName;
        $this->variantCode = $variantCode;
        $this->variantOptions = $variantOptions;
        $this->itemNumber = $itemNumber;
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

    public function type(): string
    {
        return $this->type;
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

    public function variantOptions(): array
    {
        return $this->variantOptions ?? [];
    }

    public function itemNumber(): ?string
    {
        return $this->itemNumber;
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

    public function promotionTotal(): int
    {
        return $this->promotionTotal;
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
