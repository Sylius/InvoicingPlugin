<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\CommandHandler;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

final class SendInvoiceEmailHandlerSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceEmailSenderInterface $emailSender
    ): void {
        $this->beConstructedWith($invoiceRepository, $orderRepository, $emailSender);
    }

    function it_requests_an_email_with_an_invoice_to_be_sent(
        RepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceEmailSenderInterface $emailSender,
        InvoiceInterface $invoice,
        OrderInterface $order,
        CustomerInterface $customer
    ): void {
        $invoiceRepository->findOneBy(['orderNumber' => '0000001'])->willReturn($invoice);

        $orderRepository->findOneBy(['number' => '0000001'])->willReturn($order);

        $order->getCustomer()->willReturn($customer);

        $customer->getEmail()->willReturn('shop@example.com');

        $emailSender->sendInvoiceEmail($invoice, 'shop@example.com')->shouldBeCalled();

        $this->__invoke(new SendInvoiceEmail('0000001', new \DateTime('now')));
    }
}
