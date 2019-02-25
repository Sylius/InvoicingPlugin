<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Creator;

interface InvoicesForPreviouslyPlacedOrdersCreatorInterface
{
    public function __invoke(array $orders): void;
}
