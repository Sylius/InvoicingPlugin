<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Generator;

use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;

interface InvoicePdfFileGeneratorInterface
{
    public function generate(InvoiceInterface $invoice): InvoicePdf;
}
