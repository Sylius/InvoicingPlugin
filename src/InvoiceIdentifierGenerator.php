<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin;

interface InvoiceIdentifierGenerator
{
    public function __invoke(string $orderId): string;
}
