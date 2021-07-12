<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Provider;

use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

interface InvoiceFilePathProviderInterface
{
    public function provide(InvoiceInterface $invoice): string;
}
