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

use PHPUnit\Framework\Attributes\DataProvider;
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
    private const ORDER_NUMBER = '0000001';

    private const CUSTOMER_EMAIL = 'customer@example.com';

    private InvoiceRepositoryInterface&MockObject $invoiceRepository;

    private MockObject&OrderRepositoryInterface $orderRepository;

    private InvoiceEmailSenderInterface&MockObject $emailSender;

    private MockObject&OrderInterface $order;

    private CustomerInterface&MockObject $customer;

    private InvoiceInterface&MockObject $invoice;

    private SendInvoiceEmailHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceRepository = $this->createMock(InvoiceRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->emailSender = $this->createMock(InvoiceEmailSenderInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->customer = $this->createMock(CustomerInterface::class);
        $this->invoice = $this->createMock(InvoiceInterface::class);

        $this->handler = new SendInvoiceEmailHandler(
            $this->invoiceRepository,
            $this->orderRepository,
            $this->emailSender,
        );
    }

    #[Test]
    #[DataProvider('attemptProvider')]
    public function it_sends_invoice_email_with_different_attempts(int $attempt): void
    {
        $this->expectOrderFound();
        $this->expectCustomerFound();
        $this->expectInvoiceFound();

        $this->invoice
            ->expects(self::once())
            ->method('isPdfSent')
            ->willReturn(false);

        $this->invoiceRepository
            ->expects(self::never())
            ->method('add');

        $this->emailSender
            ->expects(self::once())
            ->method('sendInvoiceEmail')
            ->with($this->invoice, self::CUSTOMER_EMAIL, $attempt);

        ($this->handler)(new SendInvoiceEmail(self::ORDER_NUMBER, $attempt));
    }

    #[Test]
    #[DataProvider('pdfSentStatusProvider')]
    public function it_persists_invoice_only_when_pdf_was_sent(bool $pdfSent, bool $shouldPersist): void
    {
        $this->expectOrderFound();
        $this->expectCustomerFound();
        $this->expectInvoiceFound();

        $this->invoice
            ->expects(self::once())
            ->method('isPdfSent')
            ->willReturn($pdfSent);

        if ($shouldPersist) {
            $this->invoiceRepository
                ->expects(self::once())
                ->method('add')
                ->with($this->invoice);
        } else {
            $this->invoiceRepository
                ->expects(self::never())
                ->method('add');
        }

        $this->emailSender
            ->expects(self::once())
            ->method('sendInvoiceEmail')
            ->with($this->invoice, self::CUSTOMER_EMAIL, 0);

        ($this->handler)(new SendInvoiceEmail(self::ORDER_NUMBER));
    }

    #[Test]
    public function it_does_not_send_email_when_order_not_found(): void
    {
        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with(self::ORDER_NUMBER)
            ->willReturn(null);

        $this->invoiceRepository
            ->expects(self::never())
            ->method('findOneByOrder');

        $this->emailSender
            ->expects(self::never())
            ->method('sendInvoiceEmail');

        $this->invoiceRepository
            ->expects(self::never())
            ->method('add');

        ($this->handler)(new SendInvoiceEmail(self::ORDER_NUMBER));
    }

    #[Test]
    public function it_does_not_send_email_when_customer_not_found(): void
    {
        $this->expectOrderFound();

        $this->order
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn(null);

        $this->invoiceRepository
            ->expects(self::never())
            ->method('findOneByOrder');

        $this->emailSender
            ->expects(self::never())
            ->method('sendInvoiceEmail');

        $this->invoiceRepository
            ->expects(self::never())
            ->method('add');

        ($this->handler)(new SendInvoiceEmail(self::ORDER_NUMBER));
    }

    #[Test]
    public function it_does_not_send_email_when_invoice_not_found(): void
    {
        $this->expectOrderFound();

        $this->order
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn($this->customer);

        $this->invoiceRepository
            ->expects(self::once())
            ->method('findOneByOrder')
            ->with($this->order)
            ->willReturn(null);

        $this->emailSender
            ->expects(self::never())
            ->method('sendInvoiceEmail');

        $this->invoiceRepository
            ->expects(self::never())
            ->method('add');

        ($this->handler)(new SendInvoiceEmail(self::ORDER_NUMBER));
    }

    #[Test]
    public function it_does_not_send_email_when_customer_email_is_null(): void
    {
        $this->expectOrderFound();

        $this->order
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn($this->customer);

        $this->customer
            ->expects(self::once())
            ->method('getEmail')
            ->willReturn(null);

        $this->invoiceRepository
            ->expects(self::once())
            ->method('findOneByOrder')
            ->with($this->order)
            ->willReturn($this->invoice);

        $this->emailSender
            ->expects(self::never())
            ->method('sendInvoiceEmail');

        $this->invoiceRepository
            ->expects(self::never())
            ->method('add');

        ($this->handler)(new SendInvoiceEmail(self::ORDER_NUMBER));
    }

    public static function attemptProvider(): array
    {
        return [
            'first attempt' => [0],
            'second attempt' => [1],
            'third attempt' => [2],
        ];
    }

    public static function pdfSentStatusProvider(): array
    {
        return [
            'pdf not sent' => [false, false],
            'pdf sent successfully' => [true, true],
        ];
    }

    private function expectOrderFound(): void
    {
        $this->orderRepository
            ->expects(self::once())
            ->method('findOneByNumber')
            ->with(self::ORDER_NUMBER)
            ->willReturn($this->order);
    }

    private function expectCustomerFound(): void
    {
        $this->order
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn($this->customer);

        $this->customer
            ->expects(self::once())
            ->method('getEmail')
            ->willReturn(self::CUSTOMER_EMAIL);
    }

    private function expectInvoiceFound(): void
    {
        $this->invoiceRepository
            ->expects(self::once())
            ->method('findOneByOrder')
            ->with($this->order)
            ->willReturn($this->invoice);
    }
}
