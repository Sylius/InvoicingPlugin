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

    /** @phpstan-ignore-next-line  */
    public function __invoke(CompletedEvent $event): void
    {
        /** @phpstan-ignore-next-line  */
        $payment = $event->getSubject();
        Assert::isInstanceOf($payment, PaymentInterface::class);

        $this->orderPaymentPaidProducer->__invoke($payment);
    }
}
