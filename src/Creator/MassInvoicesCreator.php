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

namespace Sylius\InvoicingPlugin\Creator;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Exception\InvoiceAlreadyGenerated;

final class MassInvoicesCreator implements MassInvoicesCreatorInterface
{
    private InvoiceCreatorInterface $invoiceCreator;

    private DateTimeProvider $dateTimeProvider;

    public function __construct(
        InvoiceCreatorInterface $invoiceCreator,
        DateTimeProvider $dateTimeProvider
    ) {
        $this->invoiceCreator = $invoiceCreator;
        $this->dateTimeProvider = $dateTimeProvider;
    }

    public function __invoke(array $orders): void
    {
        /** @var OrderInterface $order */
        foreach ($orders as $order) {
            try {
                $this->invoiceCreator->__invoke($order->getNumber(), $this->dateTimeProvider->__invoke());
            } catch (InvoiceAlreadyGenerated $exception) {
                continue;
            }
        }
    }
}
