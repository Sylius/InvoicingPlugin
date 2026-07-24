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

namespace Sylius\InvoicingPlugin\EventProducer;

use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class OrderPaymentPaidProducer
{
    public function __construct(
        private readonly MessageBusInterface $eventBus,
        private readonly ClockInterface $clock,
        private readonly InvoiceRepositoryInterface $invoiceRepository,
    ) {
    }

    public function __invoke(PaymentInterface $payment): void
    {
        if (!$this->shouldEventBeDispatched($payment)) {
            return;
        }

        $order = $payment->getOrder();

        Assert::notNull($order);

        $this->eventBus->dispatch(new OrderPaymentPaid(
            $order->getNumber(),
            $this->clock->now(),
        ));
    }

    private function shouldEventBeDispatched(PaymentInterface $payment): bool
    {
        $order = $payment->getOrder();
        if (null === $order) {
            return false;
        }

        return $order->getPaymentState() === OrderPaymentStates::STATE_PAID && null !== $this->invoiceRepository->findOneByOrder($order);
    }
}
