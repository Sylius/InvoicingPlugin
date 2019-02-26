<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Creator;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Exception\InvoiceAlreadyGenerated;

final class MassInvoicesCreator implements MassInvoicesCreatorInterface
{
    /** @var InvoiceCreatorInterface */
    private $invoiceCreator;

    /** @var DateTimeProvider */
    private $dateTimeProvider;

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
