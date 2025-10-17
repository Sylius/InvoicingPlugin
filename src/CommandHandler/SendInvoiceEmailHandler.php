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

namespace Sylius\InvoicingPlugin\CommandHandler;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

final readonly class SendInvoiceEmailHandler
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private OrderRepositoryInterface $orderRepository,
        private InvoiceEmailSenderInterface $emailSender,
    ) {
    }

    public function __invoke(SendInvoiceEmail $command): void
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByNumber($command->orderNumber());
        if (null === $order) {
            return;
        }

        $customer = $order->getCustomer();
        if (null === $customer) {
            return;
        }

        /** @var InvoiceInterface|null $invoice */
        $invoice = $this->invoiceRepository->findOneByOrder($order);
        if (null === $invoice) {
            return;
        }

        $customerEmail = $customer->getEmail();
        if (null === $customerEmail) {
            return;
        }

        $this->emailSender->sendInvoiceEmail($invoice, $customerEmail, $command->attempt());

        if ($invoice->isPdfSent()) {
            $this->invoiceRepository->add($invoice);
        }
    }
}
