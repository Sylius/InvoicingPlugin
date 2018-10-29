<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Generator;

use Sylius\InvoicingPlugin\Model\InvoicePdf;

interface InvoicePdfFileGeneratorInterface
{
    public function generate(string $invoiceId): InvoicePdf;
}
