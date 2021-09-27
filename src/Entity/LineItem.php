<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\InvoicingPlugin\Exception\LineItemsCannotBeMerged;

/** @final */
class LineItem implements LineItemInterface, ResourceInterface
{
    protected ?int $id;

    protected InvoiceInterface $invoice;

    protected string $name;

    protected ?string $variantName;

    protected ?string $variantCode;

    protected int $quantity;

    protected int $unitPrice;

    protected int $subtotal;

    protected ?string $taxRate;

    protected int $taxTotal;

    protected int $total;

    public function __construct(
        string $name,
        int $quantity,
        int $unitPrice,
        int $subtotal,
        int $taxTotal,
        int $total,
        ?string $variantName = null,
        ?string $variantCode = null,
        ?string $taxRate = null
    ) {
        $this->name = $name;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
        $this->subtotal = $subtotal;
        $this->taxTotal = $taxTotal;
        $this->total = $total;
        $this->variantName = $variantName;
        $this->variantCode = $variantCode;
        $this->taxRate = $taxRate;
    }

    public function getId(): ?int
    {
        return $this->id();
    }

    public function id(): ?int
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

    public function taxRate(): ?string
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

    public function merge(LineItemInterface $newLineItem): void
    {
        if (!$this->compare($newLineItem)) {
            throw LineItemsCannotBeMerged::occur();
        }

        $this->quantity += $newLineItem->quantity();
        $this->subtotal += $newLineItem->subtotal();
        $this->total += $newLineItem->total();
        $this->taxTotal += $newLineItem->taxTotal();
    }

    public function compare(LineItemInterface $lineItem): bool
    {
        return
            $this->name() === $lineItem->name() &&
            $this->unitPrice() === $lineItem->unitPrice() &&
            $this->taxRate() === $lineItem->taxRate()
        ;
    }
}
