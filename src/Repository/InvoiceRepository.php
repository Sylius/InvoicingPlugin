<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Repository;

@trigger_error('The "InvoiceRepository" interface is deprecated since version 1.0.0 Use standardized interface located at "src/Doctrine/ORM/" instead.');

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

interface InvoiceRepository extends RepositoryInterface
{
    public function get(string $invoiceId): InvoiceInterface;

    public function findOneByOrderNumber(string $orderNumber): ?InvoiceInterface;
}
