<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Sylius\InvoicingPlugin\EventListener\OrderPaymentPaidListener;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class OrderPaymentPaidListenerSpec extends ObjectBehavior
{
    function let(
        InvoiceRepository $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceEmailSenderInterface $emailSender
    ): void {
        $this->beConstructedWith($invoiceRepository, $orderRepository, $emailSender);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(OrderPaymentPaidListener::class);
    }

    function it_requests_an_email_with_an_invoice_to_be_sent(
        InvoiceRepository $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceEmailSenderInterface $emailSender,
        OrderPaymentPaid $event,
        InvoiceInterface $invoice,
        OrderInterface $order,
        CustomerInterface $customer
    ): void {
        $event->orderNumber()->willReturn('0000001');

        $invoiceRepository->getOneByOrderNumber('0000001')->willReturn($invoice);

        $orderRepository->findOneBy(['number' => '0000001'])->willReturn($order);

        $order->getCustomer()->willReturn($customer);

        $customer->getEmail()->willReturn('shop@example.com');

        $emailSender->sendInvoiceEmail($invoice, 'shop@example.com')->shouldBeCalled();

        $this->__invoke($event);
    }
}
