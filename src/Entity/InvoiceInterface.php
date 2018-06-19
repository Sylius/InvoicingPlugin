<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

interface InvoiceInterface
{
    public function id(): string;

    public function orderNumber(): string;

    public function issuedAt(): \DateTimeInterface;
}
