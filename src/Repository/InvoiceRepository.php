<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Repository;

use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

interface InvoiceRepository
{
    public function get(string $invoiceId): InvoiceInterface;

    public function findOneByOrderNumber(string $orderNumber): ?InvoiceInterface;

    public function add(InvoiceInterface $invoice): void;
}
