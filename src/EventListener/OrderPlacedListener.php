<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventListener;

use Sylius\InvoicingPlugin\Event\OrderPlaced;

final class OrderPlacedListener
{
    public function __invoke(OrderPlaced $event): void
    {

    }
}
