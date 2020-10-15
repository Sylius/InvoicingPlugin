<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Repository;

@trigger_error('The "InvoiceRepository" interface is deprecated since version 0.11.1 Use standardized interface located at "src/Doctrine/ORM/" instead.');

use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

interface InvoiceRepository
{
    public function get(string $invoiceId): InvoiceInterface;

    public function findOneByOrderNumber(string $orderNumber): ?InvoiceInterface;

    public function add(InvoiceInterface $invoice): void;
}
