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
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Exception\InvoiceFileGenerationFailedException;
use Sylius\InvoicingPlugin\Provider\InvoiceFileProviderInterface;
use Webmozart\Assert\Assert;

final class InvoiceEmailSender implements InvoiceEmailSenderInterface
{
    public function __construct(
        private readonly SenderInterface $emailSender,
        private readonly InvoiceFileProviderInterface $invoiceFileProvider,
        private readonly bool $hasEnabledPdfFileGenerator = true,
        private readonly ?LoggerInterface $logger = null,
        private readonly ?InvoiceEmailRetrySchedulerInterface $retryScheduler = null,
    ) {
    }

    public function sendInvoiceEmail(InvoiceInterface $invoice, string $customerEmail, int $attempt = 0): void
    {
        if (!$this->hasEnabledPdfFileGenerator) {
            $this->emailSender->send(
                Emails::INVOICE_GENERATED,
                [$customerEmail],
                ['invoice' => $invoice],
                [],
                [],
                [],
                [],
            );

            return;
        }

        try {
            $invoicePdf = $this->invoiceFileProvider->provide($invoice);
            $invoicePdfPath = $invoicePdf->fullPath();
            Assert::notNull($invoicePdfPath);

            if (!is_file($invoicePdfPath) || !is_readable($invoicePdfPath)) {
                throw InvoiceFileGenerationFailedException::forInvoice(
                    $invoice,
                    new \RuntimeException(sprintf('Invoice PDF file "%s" is not readable.', $invoicePdfPath)),
                );
            }

            $this->emailSender->send(
                Emails::INVOICE_GENERATED,
                [$customerEmail],
                ['invoice' => $invoice],
                [$invoicePdfPath],
                [],
                [],
                [],
            );

            $invoice->setPdfSent(true);
        } catch (InvoiceFileGenerationFailedException $exception) {
            if (null !== $this->logger) {
                $this->logger->error(
                    sprintf(
                        'Invoice PDF for invoice "%s" (%s) could not be generated. Email to "%s" will not be sent.',
                        $invoice->number(),
                        $invoice->id(),
                        $customerEmail,
                    ),
                    ['exception' => $exception],
                );
            }

            if (null !== $this->retryScheduler) {
                $this->retryScheduler->scheduleRetry($invoice, $customerEmail, $attempt);
            }

            $invoice->setPdfSent(false);
        }
    }
}
