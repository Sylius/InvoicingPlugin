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

interface LineItemInterface
{
    public function id(): string;

    public function invoice(): InvoiceInterface;

    public function setInvoice(InvoiceInterface $invoice): void;

    public function name(): string;

    public function variantName(): ?string;

    public function variantCode(): ?string;

    public function quantity(): int;

    public function unitPrice(): int;

    public function subtotal(): int;

    public function taxRate(): ?string;

    public function taxTotal(): int;

    public function total(): int;

    public function merge(self $newLineItem): void;

    public function compare(self $lineItem): bool;
}
