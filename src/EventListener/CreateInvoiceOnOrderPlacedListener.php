<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventListener;

use Sylius\InvoicingPlugin\Creator\InvoiceCreatorInterface;
use Sylius\InvoicingPlugin\Event\OrderPlaced;
use Sylius\InvoicingPlugin\Exception\InvoiceAlreadyGenerated;

final class CreateInvoiceOnOrderPlacedListener
{
    public function __construct(private readonly InvoiceCreatorInterface $invoiceCreator)
    {
    }

    public function __invoke(OrderPlaced $event): void
    {
        try {
            $this->invoiceCreator->__invoke($event->orderNumber(), $event->date());
        } catch (InvoiceAlreadyGenerated) {
            return;
        }
    }
}
