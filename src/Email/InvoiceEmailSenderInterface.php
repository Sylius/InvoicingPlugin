<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Email;

use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

interface InvoiceEmailSenderInterface
{
    public function sendInvoiceEmail(InvoiceInterface $invoice, string $customerEmail): void;
}
