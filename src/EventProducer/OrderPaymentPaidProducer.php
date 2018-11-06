<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventProducer;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderPaymentPaidProducer
{
    /** @var MessageBusInterface */
    private $eventBus;

    /** @var DateTimeProvider */
    private $dateTimeProvider;

    public function __construct(MessageBusInterface $eventBus, DateTimeProvider $dateTimeProvider)
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
            $this->dateTimeProvider->__invoke()
        ));
    }
}
