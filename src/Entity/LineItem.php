<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

class LineItem implements LineItemInterface, ResourceInterface
{
    public const TYPE_ITEM = 'item';
    public const TYPE_SHIPPING = 'shipping';
    public const TYPE_PAYMENT = 'payment';
    public const TYPE_PROMOTION = 'promotion';

    /** @var string */
    protected $id;

    /** @var InvoiceInterface */
    protected $invoice;

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

    /** @var float|null */
    protected $taxRate;

    /** @var int */
    protected $taxTotal;

    /** @var int */
    protected $total;

    /** @var string|null */
    protected $accountingNumber;

    /** @var int */
    protected $retainedFee;

    public function __construct(
        string $type,
        string $name,
        int $quantity,
        int $unitPrice,
        int $subtotal,
        int $promotionTotal,
        ?float $taxRate,
        int $taxTotal,
        int $total,
        ?string $variantName = null,
        ?string $variantCode = null,
        array $variantOptions = [],
        ?string $itemNumber = null,
        ?string $accountingNumber = null,
        int $retainedFee = 0
    ) {
        $this->type = $type;
        $this->name = $name;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->subtotal = $subtotal;
        $this->promotionTotal = $promotionTotal;
        $this->taxRate = $taxRate;
        $this->taxTotal = $taxTotal;
        $this->total = $total;
        $this->variantName = $variantName;
        $this->variantCode = $variantCode;
        $this->variantOptions = $variantOptions;
        $this->itemNumber = $itemNumber;
        $this->accountingNumber = $accountingNumber;
        $this->retainedFee = $retainedFee;
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

    public function taxRate(): ?float
    {
        return $this->taxRate;
    }

    public function taxTotal(): int
    {
        return $this->taxTotal;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function accountingNumber(): ?string
    {
        return $this->accountingNumber;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param null|string $variantName
     */
    public function setVariantName(?string $variantName): void
    {
        $this->variantName = $variantName;
    }

    /**
     * @param null|string $variantCode
     */
    public function setVariantCode(?string $variantCode): void
    {
        $this->variantCode = $variantCode;
    }

    /**
     * @param array|null $variantOptions
     */
    public function setVariantOptions(?array $variantOptions): void
    {
        $this->variantOptions = $variantOptions;
    }

    /**
     * @param null|string $itemNumber
     */
    public function setItemNumber(?string $itemNumber): void
    {
        $this->itemNumber = $itemNumber;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @param int $unitPrice
     */
    public function setUnitPrice(int $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }

    /**
     * @param int $subtotal
     */
    public function setSubtotal(int $subtotal): void
    {
        $this->subtotal = $subtotal;
    }

    /**
     * @param int $promotionTotal
     */
    public function setPromotionTotal(int $promotionTotal): void
    {
        $this->promotionTotal = $promotionTotal;
    }

    /**
     * @param float|null $taxRate
     */
    public function setTaxRate(?float $taxRate): void
    {
        $this->taxRate = $taxRate;
    }

    /**
     * @param int $taxTotal
     */
    public function setTaxTotal(int $taxTotal): void
    {
        $this->taxTotal = $taxTotal;
    }

    /**
     * @param int $total
     */
    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    /**
     * @param null|string $accountingNumber
     */
    public function setAccountingNumber(?string $accountingNumber): void
    {
        $this->accountingNumber = $accountingNumber;
    }

    public function getRetainedFee(): int
    {
        return $this->retainedFee;
    }

    public function setRetainedFee(int $retainedFee): self
    {
        $this->retainedFee = $retainedFee;

        return $this;
    }
}
