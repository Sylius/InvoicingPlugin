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

namespace Tests\Sylius\InvoicingPlugin\Unit\CommandHandler;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;
use Sylius\InvoicingPlugin\CommandHandler\SendInvoiceEmailHandler;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

final class SendInvoiceEmailHandlerTest extends TestCase
{
    private InvoiceRepositoryInterface&MockObject $invoiceRepository;

    private MockObject&OrderRepositoryInterface $orderRepository;

    private InvoiceEmailSenderInterface&MockObject $emailSender;

    private SendInvoiceEmailHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->emailSender = $this->createMock(InvoiceEmailSenderInterface::class);

        $this->handler = new SendInvoiceEmailHandler(
            $this->invoiceRepository,
            $this->orderRepository,
            $this->emailSender,
        );
    }

    #[Test]
    public function it_requests_an_email_with_an_invoice_to_be_sent(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $customer = $this->createMock(CustomerInterface::class);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('0000001')
            ->willReturn($order);

        $order
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn($customer);

        $customer
            ->expects(self::once())
            ->method('getEmail')
            ->willReturn('shop@example.com');

        $this->invoiceRepository
            ->expects(self::once())
            ->method('findOneByOrder')
            ->with($order)
            ->willReturn($invoice);

        $this->emailSender
            ->expects(self::once())
            ->method('sendInvoiceEmail')
            ->with($invoice, 'shop@example.com');

        ($this->handler)(new SendInvoiceEmail('0000001'));
    }

    #[Test]
    public function it_does_not_request_an_email_to_be_sent_if_order_was_not_found(): void
    {
        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('0000001')
            ->willReturn(null);

        $this->invoiceRepository
            ->expects($this->never())
            ->method('findOneByOrder');

        $this->emailSender
            ->expects($this->never())
            ->method('sendInvoiceEmail');

        ($this->handler)(new SendInvoiceEmail('0000001'));
    }

    #[Test]
    public function it_does_not_request_an_email_to_be_sent_if_customer_was_not_found(): void
    {
        $order = $this->createMock(OrderInterface::class);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('0000001')
            ->willReturn($order);

        $order
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn(null);

        $this->invoiceRepository
            ->expects($this->never())
            ->method('findOneByOrder');

        $this->emailSender
            ->expects($this->never())
            ->method('sendInvoiceEmail');

        ($this->handler)(new SendInvoiceEmail('0000001'));
    }

    #[Test]
    public function it_does_not_request_an_email_to_be_sent_if_invoice_was_not_found(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $customer = $this->createMock(CustomerInterface::class);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with('0000001')
            ->willReturn($order);

        $order
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn($customer);

        $this->invoiceRepository
            ->expects(self::once())
            ->method('findOneByOrder')
            ->with($order)
            ->willReturn(null);

        $this->emailSender
            ->expects($this->never())
            ->method('sendInvoiceEmail');

        ($this->handler)(new SendInvoiceEmail('0000001'));
    }
}
