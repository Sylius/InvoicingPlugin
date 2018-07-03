<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventListener;

use Prooph\ServiceBus\EventBus;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;

final class OrderPaymentPaidProducer
{
    /** @var EventBus */
    private $eventBus;

    /** @var DateTimeProvider */
    private $dateTimeProvider;

    public function __construct(EventBus $eventBus, DateTimeProvider $dateTimeProvider)
    {
        $this->eventBus = $eventBus;
        $this->dateTimeProvider = $dateTimeProvider;
    }

    public function __invoke(PaymentInterface $payment): void
    {
        if (null === $payment->getOrder()) {
            return;
        }

        $this->eventBus->dispatch(new OrderPaymentPaid(
            $payment->getOrder()->getNumber(),
            $this->dateTimeProvider->__invoke())
        );
    }
}
