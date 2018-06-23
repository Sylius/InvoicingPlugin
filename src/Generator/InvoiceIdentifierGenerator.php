<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Generator;

interface InvoiceIdentifierGenerator
{
    public function __invoke(string $orderNumber): string;
}
