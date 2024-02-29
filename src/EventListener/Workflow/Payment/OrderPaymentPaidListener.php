<?php

namespace Sylius\InvoicingPlugin\EventListener\Workflow\Payment;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\InvoicingPlugin\EventProducer\OrderPaymentPaidProducer;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Webmozart\Assert\Assert;

final class OrderPaymentPaidListener
{
    public function __construct(
        private OrderPaymentPaidProducer $orderPaymentPaidProducer,
    ) {
    }

    public function __invoke(CompletedEvent $event)
    {
        $payment = $event->getSubject();
        Assert::isInstanceOf($payment, PaymentInterface::class);

        $this->orderPaymentPaidProducer($payment);
    }
}
