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

namespace Sylius\InvoicingPlugin\CommandHandler;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

final class SendInvoiceEmailHandler
{
    private InvoiceRepositoryInterface $invoiceRepository;

    private OrderRepositoryInterface $orderRepository;

    private InvoiceEmailSenderInterface $emailSender;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceEmailSenderInterface $emailSender
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->orderRepository = $orderRepository;
        $this->emailSender = $emailSender;
    }

    public function __invoke(SendInvoiceEmail $command): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($command->orderNumber());

        /** @var InvoiceInterface|null $invoice */
        $invoice = $this->invoiceRepository->findOneByOrder($order);

        if (null === $invoice) {
            return;
        }

        if (null === $order->getCustomer()) {
            return;
        }

        $this->emailSender->sendInvoiceEmail($invoice, $order->getCustomer()->getEmail());
    }
}
