<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Creator;

interface InvoiceForPreviouslyPlacedOrdersCreatorInterface
{
    public function __invoke(array $orders): void;
}
