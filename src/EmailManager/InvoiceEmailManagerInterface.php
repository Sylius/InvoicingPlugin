<?php
declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EmailManager;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

interface InvoiceEmailManagerInterface
{
    public function sendInvoiceEmail(InvoiceInterface $invoice, PdfResponse $invoiceAttachment, string $customerEmail): void;
}
