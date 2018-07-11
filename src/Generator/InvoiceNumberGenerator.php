<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Generator;

interface InvoiceNumberGenerator
{
    public function generate(): string;
}
