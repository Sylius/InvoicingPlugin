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

namespace Sylius\InvoicingPlugin\Email;

use Psr\Log\LoggerInterface;
use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

final class InvoiceEmailRetryScheduler implements InvoiceEmailRetrySchedulerInterface
{
    public function __construct(
        private readonly MessageBusInterface $commandBus,
        private readonly int $maxAttempts,
        private readonly int $retryDelayMilliseconds,
        private readonly ?LoggerInterface $logger = null,
    ) {
    }

    public function scheduleRetry(InvoiceInterface $invoice, string $customerEmail, int $attempt): void
    {
        if ($attempt >= $this->maxAttempts) {
            $this->logger?->warning(
                sprintf(
                    'Invoice email retry aborted for invoice "%s" (%s) after %d attempts.',
                    $invoice->number(),
                    $invoice->id(),
                    $attempt,
                ),
                ['customerEmail' => $customerEmail],
            );

            return;
        }

        $orderNumber = $invoice->order()->getNumber();
        if (null === $orderNumber) {
            $this->logger?->warning(
                sprintf(
                    'Invoice email retry skipped because order number is missing for invoice "%s" (%s).',
                    $invoice->number(),
                    $invoice->id(),
                ),
                ['customerEmail' => $customerEmail],
            );

            return;
        }

        $nextAttempt = $attempt + 1;
        $message = new SendInvoiceEmail($orderNumber, $nextAttempt);
        $envelope = new Envelope($message, [new DelayStamp($this->retryDelayMilliseconds)]);

        try {
            $this->commandBus->dispatch($envelope);
        } catch (ExceptionInterface $e) {
            $this->logger?->error($e->getMessage());
        }

        $this->logger?->info(
            sprintf(
                'Scheduled invoice email retry #%d for invoice "%s" (%s) to "%s".',
                $nextAttempt,
                $invoice->number(),
                $invoice->id(),
                $customerEmail,
            ),
        );
    }
}
