<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Checker;

use Sylius\Component\Core\Model\CustomerInterface;

interface InvoiceCustomerRelationCheckerInterface
{
    public function check(string $invoiceId, CustomerInterface $customer): void;
}
