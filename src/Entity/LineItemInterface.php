<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

interface LineItemInterface
{
    public function id(): string;

    public function invoice(): InvoiceInterface;

    public function setInvoice(InvoiceInterface $invoice): void;

    public function type(): string;

    public function name(): string;

    public function variantName(): ?string;

    public function variantCode(): ?string;

    public function variantOptions(): array;

    public function itemNumber(): ?string;

    public function quantity(): int;

    public function unitPrice(): int;

    public function subtotal(): int;

    public function promotionTotal(): int;

    public function taxTotal(): int;

    public function total(): int;
}
