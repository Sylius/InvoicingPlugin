<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
    /** @var mixed */
    protected $id;

    protected InvoiceInterface $invoice;

    public function __construct(
        protected string $name,
        protected int $quantity,
        protected int $unitNetPrice,
        protected int $discountedUnitNetPrice,
        protected int $subtotal,
        protected int $taxTotal,
        protected int $total,
        protected ?string $variantName = null,
        protected ?string $variantCode = null,
        protected ?string $taxRate = null,
    ) {
    }

    public function getId()
    {
        return $this->id();
    }

    public function id()
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
        return $this->unitNetPrice;
    }

    public function discountedUnitNetPrice(): int
    {
        return $this->discountedUnitNetPrice;
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
            $this->variantCode() === $lineItem->variantCode() &&
            $this->discountedUnitNetPrice() === $lineItem->discountedUnitNetPrice() &&
            $this->unitPrice() === $lineItem->unitPrice() &&
            $this->taxRate() === $lineItem->taxRate()
        ;
    }
}
