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

use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderPaymentPaidListener
{
    public function __construct(private readonly MessageBusInterface $commandBus)
    {
    }

    public function __invoke(OrderPaymentPaid $event): void
    {
        $this->commandBus->dispatch(new SendInvoiceEmail($event->orderNumber()));
    }
}
