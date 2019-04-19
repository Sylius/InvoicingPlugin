<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\CommandHandler;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class SendInvoiceEmailHandler
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var InvoiceEmailSenderInterface */
    private $emailSender;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        ChannelRepositoryInterface $channelRepository,
        InvoiceEmailSenderInterface $emailSender
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->orderRepository = $orderRepository;
        $this->channelRepository = $channelRepository;
        $this->emailSender = $emailSender;
    }

    public function __invoke(SendInvoiceEmail $command): void
    {
        /** @var InvoiceInterface|null $invoice */
        $invoice = $this->invoiceRepository->findOneByOrderNumber($command->orderNumber());

        if (null === $invoice) {
            return;
        }

        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($invoice->channel()->getCode());

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($command->orderNumber());

        if (null === $order->getCustomer()) {
            return;
        }

        $this->emailSender->sendInvoiceEmail($invoice, $channel, $order->getCustomer()->getEmail());
    }
}
