<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Checker;

interface InvoiceCustomerRelationCheckerInterface
{
    public function check(string $invoiceId): void;
}
