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

namespace Tests\Sylius\InvoicingPlugin\Unit\Email;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\InvoicingPlugin\Email\Emails;
use Sylius\InvoicingPlugin\Email\InvoiceEmailRetrySchedulerInterface;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSender;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Exception\InvoiceFileGenerationFailedException;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Sylius\InvoicingPlugin\Provider\InvoiceFileProviderInterface;

final class InvoiceEmailSenderTest extends TestCase
{
    private const CUSTOMER_EMAIL = 'customer@example.com';

    private const INVOICE_NUMBER = '2024/11/0001';

    private const INVOICE_ID = 'INV-0001';

    private MockObject&SenderInterface $sender;

    private InvoiceFileProviderInterface&MockObject $invoiceFileProvider;

    private InvoiceInterface&MockObject $invoice;

    private ?string $temporaryFilePath = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sender = $this->createMock(SenderInterface::class);
        $this->invoiceFileProvider = $this->createMock(InvoiceFileProviderInterface::class);
        $this->invoice = $this->createMock(InvoiceInterface::class);

        $this->invoice
            ->method('number')
            ->willReturn(self::INVOICE_NUMBER);

        $this->invoice
            ->method('id')
            ->willReturn(self::INVOICE_ID);
    }

    protected function tearDown(): void
    {
        if (null !== $this->temporaryFilePath && file_exists($this->temporaryFilePath)) {
            @unlink($this->temporaryFilePath);
        }

        parent::tearDown();
    }

    #[Test]
    public function it_implements_invoice_email_sender_interface(): void
    {
        $invoiceEmailSender = new InvoiceEmailSender($this->sender, $this->invoiceFileProvider);

        self::assertInstanceOf(InvoiceEmailSenderInterface::class, $invoiceEmailSender);
    }

    #[Test]
    #[DataProvider('attemptProvider')]
    public function it_sends_invoice_email_with_pdf_attachment(int $attempt): void
    {
        $invoiceEmailSender = new InvoiceEmailSender($this->sender, $this->invoiceFileProvider);

        $temporaryPath = $this->createTemporaryPdfFile();
        $invoicePdf = new InvoicePdf('invoice.pdf', 'PDF_CONTENT');
        $invoicePdf->setFullPath($temporaryPath);

        $this->invoiceFileProvider
            ->expects(self::once())
            ->method('provide')
            ->with($this->invoice)
            ->willReturn($invoicePdf);

        $this->invoice
            ->expects(self::once())
            ->method('setPdfSent')
            ->with(true);

        $this->sender
            ->expects(self::once())
            ->method('send')
            ->with(
                Emails::INVOICE_GENERATED,
                [self::CUSTOMER_EMAIL],
                ['invoice' => $this->invoice],
                [$temporaryPath],
                [],
                [],
                [],
            );

        $invoiceEmailSender->sendInvoiceEmail($this->invoice, self::CUSTOMER_EMAIL, $attempt);
    }

    #[Test]
    public function it_sends_invoice_email_without_pdf_when_pdf_generation_disabled(): void
    {
        $invoiceEmailSender = new InvoiceEmailSender($this->sender, $this->invoiceFileProvider, false);

        $this->invoiceFileProvider
            ->expects(self::never())
            ->method('provide');

        $this->invoice
            ->expects(self::never())
            ->method('setPdfSent');

        $this->sender
            ->expects(self::once())
            ->method('send')
            ->with(
                Emails::INVOICE_GENERATED,
                [self::CUSTOMER_EMAIL],
                ['invoice' => $this->invoice],
                [],
                [],
                [],
                [],
            );

        $invoiceEmailSender->sendInvoiceEmail($this->invoice, self::CUSTOMER_EMAIL);
    }

    #[Test]
    public function it_handles_pdf_file_not_readable_exception(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $retryScheduler = $this->createMock(InvoiceEmailRetrySchedulerInterface::class);

        $invoiceEmailSender = new InvoiceEmailSender(
            $this->sender,
            $this->invoiceFileProvider,
            true,
            $logger,
            $retryScheduler,
        );

        $invoicePdf = new InvoicePdf('invoice.pdf', 'PDF_CONTENT');
        $invoicePdf->setFullPath('/nonexistent/path/invoice.pdf');

        $this->invoiceFileProvider
            ->expects(self::once())
            ->method('provide')
            ->with($this->invoice)
            ->willReturn($invoicePdf);

        $this->invoice
            ->expects(self::once())
            ->method('setPdfSent')
            ->with(false);

        $logger
            ->expects(self::once())
            ->method('error')
            ->with(
                sprintf(
                    'Invoice PDF for invoice "%s" (%s) could not be generated. Email to "%s" will not be sent.',
                    self::INVOICE_NUMBER,
                    self::INVOICE_ID,
                    self::CUSTOMER_EMAIL,
                ),
                self::callback(fn (array $context) => isset($context['exception']) && $context['exception'] instanceof InvoiceFileGenerationFailedException),
            );

        $retryScheduler
            ->expects(self::once())
            ->method('scheduleRetry')
            ->with($this->invoice, self::CUSTOMER_EMAIL, 0);

        $this->sender
            ->expects(self::never())
            ->method('send');

        $invoiceEmailSender->sendInvoiceEmail($this->invoice, self::CUSTOMER_EMAIL);
    }

    #[Test]
    public function it_schedules_retry_when_pdf_generation_fails(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $retryScheduler = $this->createMock(InvoiceEmailRetrySchedulerInterface::class);

        $invoiceEmailSender = new InvoiceEmailSender(
            $this->sender,
            $this->invoiceFileProvider,
            true,
            $logger,
            $retryScheduler,
        );

        $this->invoiceFileProvider
            ->expects(self::once())
            ->method('provide')
            ->with($this->invoice)
            ->willThrowException(InvoiceFileGenerationFailedException::occur());

        $this->invoice
            ->expects(self::once())
            ->method('setPdfSent')
            ->with(false);

        $logger
            ->expects(self::once())
            ->method('error')
            ->with(
                sprintf(
                    'Invoice PDF for invoice "%s" (%s) could not be generated. Email to "%s" will not be sent.',
                    self::INVOICE_NUMBER,
                    self::INVOICE_ID,
                    self::CUSTOMER_EMAIL,
                ),
                self::callback(fn (array $context) => isset($context['exception']) && $context['exception'] instanceof InvoiceFileGenerationFailedException),
            );

        $retryScheduler
            ->expects(self::once())
            ->method('scheduleRetry')
            ->with($this->invoice, self::CUSTOMER_EMAIL, 0);

        $this->sender
            ->expects(self::never())
            ->method('send');

        $invoiceEmailSender->sendInvoiceEmail($this->invoice, self::CUSTOMER_EMAIL);
    }

    #[Test]
    public function it_handles_pdf_generation_failure_without_retry_scheduler(): void
    {
        $logger = $this->createMock(LoggerInterface::class);

        $invoiceEmailSender = new InvoiceEmailSender(
            $this->sender,
            $this->invoiceFileProvider,
            true,
            $logger,
            null,
        );

        $this->invoiceFileProvider
            ->expects(self::once())
            ->method('provide')
            ->with($this->invoice)
            ->willThrowException(InvoiceFileGenerationFailedException::occur());

        $this->invoice
            ->expects(self::once())
            ->method('setPdfSent')
            ->with(false);

        $logger
            ->expects(self::once())
            ->method('error');

        $this->sender
            ->expects(self::never())
            ->method('send');

        $invoiceEmailSender->sendInvoiceEmail($this->invoice, self::CUSTOMER_EMAIL);
    }

    #[Test]
    public function it_handles_pdf_generation_failure_without_logger(): void
    {
        $retryScheduler = $this->createMock(InvoiceEmailRetrySchedulerInterface::class);

        $invoiceEmailSender = new InvoiceEmailSender(
            $this->sender,
            $this->invoiceFileProvider,
            true,
            null,
            $retryScheduler,
        );

        $this->invoiceFileProvider
            ->expects(self::once())
            ->method('provide')
            ->with($this->invoice)
            ->willThrowException(InvoiceFileGenerationFailedException::occur());

        $this->invoice
            ->expects(self::once())
            ->method('setPdfSent')
            ->with(false);

        $retryScheduler
            ->expects(self::once())
            ->method('scheduleRetry')
            ->with($this->invoice, self::CUSTOMER_EMAIL, 0);

        $this->sender
            ->expects(self::never())
            ->method('send');

        $invoiceEmailSender->sendInvoiceEmail($this->invoice, self::CUSTOMER_EMAIL);
    }

    #[Test]
    public function it_handles_pdf_generation_failure_without_logger_and_retry_scheduler(): void
    {
        $invoiceEmailSender = new InvoiceEmailSender(
            $this->sender,
            $this->invoiceFileProvider,
            true,
            null,
            null,
        );

        $this->invoiceFileProvider
            ->expects(self::once())
            ->method('provide')
            ->with($this->invoice)
            ->willThrowException(InvoiceFileGenerationFailedException::occur());

        $this->invoice
            ->expects(self::once())
            ->method('setPdfSent')
            ->with(false);

        $this->sender
            ->expects(self::never())
            ->method('send');

        $invoiceEmailSender->sendInvoiceEmail($this->invoice, self::CUSTOMER_EMAIL);
    }

    public static function attemptProvider(): array
    {
        return [
            'first attempt' => [0],
            'second attempt' => [1],
            'third attempt' => [2],
        ];
    }

    private function createTemporaryPdfFile(): string
    {
        $this->temporaryFilePath = tempnam(sys_get_temp_dir(), 'invoice_pdf_');
        self::assertNotFalse($this->temporaryFilePath);
        self::assertNotFalse(file_put_contents($this->temporaryFilePath, 'PDF_CONTENT'));

        return $this->temporaryFilePath;
    }
}
