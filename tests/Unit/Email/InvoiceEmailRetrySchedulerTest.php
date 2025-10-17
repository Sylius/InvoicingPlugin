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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;
use Sylius\InvoicingPlugin\Email\InvoiceEmailRetryScheduler;
use Sylius\InvoicingPlugin\Email\InvoiceEmailRetrySchedulerInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class InvoiceEmailRetrySchedulerTest extends TestCase
{
    private MessageBusInterface&MockObject $commandBus;

    private LoggerInterface&MockObject $logger;

    private InvoiceInterface&MockObject $invoice;

    private MockObject&OrderInterface $order;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commandBus = $this->createMock(MessageBusInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->invoice = $this->createMock(InvoiceInterface::class);
        $this->order = $this->createMock(OrderInterface::class);

        $this->invoice
            ->method('order')
            ->willReturn($this->order);

        $this->invoice
            ->method('number')
            ->willReturn('2024/11/0001');

        $this->invoice
            ->method('id')
            ->willReturn('INV-0001');
    }

    #[Test]
    public function it_implements_invoice_email_retry_scheduler_interface(): void
    {
        $scheduler = new InvoiceEmailRetryScheduler($this->commandBus, 3, 60000);

        self::assertInstanceOf(InvoiceEmailRetrySchedulerInterface::class, $scheduler);
    }

    #[Test]
    #[DataProvider('retryAttemptProvider')]
    public function it_schedules_retry_with_correct_attempt_number_and_delay(
        int $currentAttempt,
        int $expectedNextAttempt,
        int $retryDelay,
    ): void {
        $scheduler = new InvoiceEmailRetryScheduler($this->commandBus, 3, $retryDelay, $this->logger);

        $this->order
            ->method('getNumber')
            ->willReturn('0000001');

        $this->commandBus
            ->expects(self::once())
            ->method('dispatch')
            ->with(self::callback(function (Envelope $envelope) use ($expectedNextAttempt, $retryDelay) {
                $message = $envelope->getMessage();
                $stamps = $envelope->all(DelayStamp::class);

                return $message instanceof SendInvoiceEmail &&
                    $message->orderNumber() === '0000001' &&
                    $message->attempt() === $expectedNextAttempt &&
                    count($stamps) === 1 &&
                    $stamps[0]->getDelay() === $retryDelay;
            }))
            ->willReturn(new Envelope(new SendInvoiceEmail('0000001', $expectedNextAttempt)));

        $this->logger
            ->expects(self::once())
            ->method('info')
            ->with(
                sprintf(
                    'Scheduled invoice email retry #%d for invoice "2024/11/0001" (INV-0001) to "customer@example.com".',
                    $expectedNextAttempt,
                ),
            );

        $scheduler->scheduleRetry($this->invoice, 'customer@example.com', $currentAttempt);
    }

    #[Test]
    #[DataProvider('abortScenarioProvider')]
    public function it_aborts_retry_when_max_attempts_reached(
        int $attempt,
        string $orderNumber,
        string $expectedWarningMessage,
    ): void {
        $scheduler = new InvoiceEmailRetryScheduler($this->commandBus, 3, 60000, $this->logger);

        $this->order
            ->method('getNumber')
            ->willReturn($orderNumber);

        $this->commandBus
            ->expects(self::never())
            ->method('dispatch');

        $this->logger
            ->expects(self::once())
            ->method('warning')
            ->with(
                $expectedWarningMessage,
                ['customerEmail' => 'customer@example.com'],
            );

        $scheduler->scheduleRetry($this->invoice, 'customer@example.com', $attempt);
    }

    #[Test]
    public function it_skips_retry_when_order_number_is_missing(): void
    {
        $scheduler = new InvoiceEmailRetryScheduler($this->commandBus, 3, 60000, $this->logger);

        $this->order
            ->method('getNumber')
            ->willReturn(null);

        $this->commandBus
            ->expects(self::never())
            ->method('dispatch');

        $this->logger
            ->expects(self::once())
            ->method('warning')
            ->with(
                'Invoice email retry skipped because order number is missing for invoice "2024/11/0001" (INV-0001).',
                ['customerEmail' => 'customer@example.com'],
            );

        $scheduler->scheduleRetry($this->invoice, 'customer@example.com', 0);
    }

    #[Test]
    public function it_logs_error_when_dispatch_fails(): void
    {
        $scheduler = new InvoiceEmailRetryScheduler($this->commandBus, 3, 60000, $this->logger);

        $this->order
            ->method('getNumber')
            ->willReturn('0000001');

        $exception = new class('Transport failed') extends \Exception implements ExceptionInterface {
        };

        $this->commandBus
            ->expects(self::once())
            ->method('dispatch')
            ->willThrowException($exception);

        $this->logger
            ->expects(self::once())
            ->method('error')
            ->with('Transport failed');

        $this->logger
            ->expects(self::once())
            ->method('info')
            ->with(
                'Scheduled invoice email retry #1 for invoice "2024/11/0001" (INV-0001) to "customer@example.com".',
            );

        $scheduler->scheduleRetry($this->invoice, 'customer@example.com', 0);
    }

    #[Test]
    public function it_works_without_logger(): void
    {
        $scheduler = new InvoiceEmailRetryScheduler($this->commandBus, 3, 60000);

        $this->order
            ->method('getNumber')
            ->willReturn('0000001');

        $this->commandBus
            ->expects(self::once())
            ->method('dispatch')
            ->willReturn(new Envelope(new SendInvoiceEmail('0000001', 1)));

        $scheduler->scheduleRetry($this->invoice, 'customer@example.com', 0);
    }

    public static function retryAttemptProvider(): array
    {
        return [
            'first attempt' => [0, 1, 60000],
            'second attempt' => [1, 2, 60000],
            'third attempt' => [2, 3, 60000],
            'custom delay' => [0, 1, 120000],
        ];
    }

    public static function abortScenarioProvider(): array
    {
        return [
            'max attempts reached' => [
                3,
                '0000001',
                'Invoice email retry aborted for invoice "2024/11/0001" (INV-0001) after 3 attempts.',
            ],
            'attempts exceeded' => [
                5,
                '0000001',
                'Invoice email retry aborted for invoice "2024/11/0001" (INV-0001) after 5 attempts.',
            ],
        ];
    }
}
