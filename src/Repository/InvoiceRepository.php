<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Repository;

use Sylius\InvoicingPlugin\Entity\Invoice;

interface InvoiceRepository
{
    public function add(Invoice $invoice): void;
}
