<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Generator;

use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

interface InvoiceFileNameGeneratorInterface
{
    public function generateForPdf(InvoiceInterface $invoice): string;
}
