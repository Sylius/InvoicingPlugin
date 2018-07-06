<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventListener;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class OrderPaymentPaidListener
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var InvoiceEmailSenderInterface */
    private $emailSender;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceEmailSenderInterface $emailSender
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->emailSender = $emailSender;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(OrderPaymentPaid $event)
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $this->invoiceRepository->getOneByOrderNumber($event->orderNumber());

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['number' => $event->orderNumber()]);

        if (null === $order->getCustomer()) {
            return;
        }

        $this->emailSender->sendInvoiceEmail($invoice, $order->getCustomer()->getEmail());
    }
}
