<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\CommandHandler;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

final class SendInvoiceEmailHandler
{
    /** @var RepositoryInterface */
    private $invoiceRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var InvoiceEmailSenderInterface */
    private $emailSender;

    public function __construct(
        RepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceEmailSenderInterface $emailSender
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->emailSender = $emailSender;
        $this->orderRepository = $orderRepository;
    }

    public function __invoke(SendInvoiceEmail $command): void
    {
        $invoice = $this->invoiceRepository->findOneBy(['orderNumber' => $command->orderNumber()]);

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneBy(['number' => $command->orderNumber()]);

        if (null === $order->getCustomer()) {
            return;
        }

        $this->emailSender->sendInvoiceEmail($invoice, $order->getCustomer()->getEmail());
    }
}
