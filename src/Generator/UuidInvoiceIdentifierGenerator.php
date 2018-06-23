<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Generator;

use Ramsey\Uuid\Uuid;

final class UuidInvoiceIdentifierGenerator implements InvoiceIdentifierGenerator
{
    public function __invoke(string $orderNumber): string
    {
        return Uuid::getFactory()->uuid4()->toString();
    }
}
