<?php
declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EmailManager;

use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

interface InvoiceEmailManagerInterface
{
    public function sendInvoiceEmail(InvoiceInterface $invoice, string $invoiceAttachmentPath, string $customerEmail): void;
}
