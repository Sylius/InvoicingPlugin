<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\ResponseBuilder;

use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Symfony\Component\HttpFoundation\Response;

interface InvoiceFileResponseBuilderInterface
{
    public function build(int $status, InvoicePdf $invoicePdf): Response;
}
