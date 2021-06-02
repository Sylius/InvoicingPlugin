<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
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
    /** @var InvoiceCreatorInterface */
    private $invoiceCreator;

    public function __construct(InvoiceCreatorInterface $invoiceCreator)
    {
        $this->invoiceCreator = $invoiceCreator;
    }

    public function __invoke(OrderPlaced $event): void
    {
        try {
            $this->invoiceCreator->__invoke($event->orderNumber(), $event->date());
        } catch (InvoiceAlreadyGenerated $exception) {
            return;
        }
    }
}
