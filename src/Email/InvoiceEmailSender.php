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

namespace Sylius\InvoicingPlugin\Email;

use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Provider\InvoiceFileProviderInterface;
use Webmozart\Assert\Assert;

final class InvoiceEmailSender implements InvoiceEmailSenderInterface
{
    private SenderInterface $emailSender;

    private InvoiceFileProviderInterface $invoiceFileProvider;

    public function __construct(
        SenderInterface $emailSender,
        InvoiceFileProviderInterface $invoiceFileProvider
    ) {
        $this->emailSender = $emailSender;
        $this->invoiceFileProvider = $invoiceFileProvider;
    }

    public function sendInvoiceEmail(
        InvoiceInterface $invoice,
        string $customerEmail
    ): void {
        $invoicePdf = $this->invoiceFileProvider->provide($invoice);
        $invoicePdfPath = $invoicePdf->fullPath();
        Assert::notNull($invoicePdfPath);

        $this->emailSender->send(Emails::INVOICE_GENERATED, [$customerEmail], ['invoice' => $invoice], [$invoicePdfPath]);
    }
}
