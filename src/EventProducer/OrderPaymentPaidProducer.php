<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventProducer;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class OrderPaymentPaidProducer
{
    /** @var MessageBusInterface */
    private $eventBus;

    /** @var DateTimeProvider */
    private $dateTimeProvider;

    /** @var InvoiceRepository */
    private $invoiceRepository;

    public function __construct(
        MessageBusInterface $eventBus,
        DateTimeProvider $dateTimeProvider,
        InvoiceRepository $invoiceRepository
    ) {
        $this->eventBus = $eventBus;
        $this->dateTimeProvider = $dateTimeProvider;
        $this->invoiceRepository = $invoiceRepository;
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
            $this->dateTimeProvider->__invoke()
        ));
    }

    private function shouldEventBeDispatched(PaymentInterface $payment): bool
    {
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        return null !== $order && null !== $this->invoiceRepository->findOneByOrderNumber($order->getNumber());
    }
}
