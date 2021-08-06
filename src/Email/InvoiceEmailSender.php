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
use Sylius\InvoicingPlugin\Filesystem\TemporaryFilesystem;

final class InvoiceEmailSender implements InvoiceEmailSenderInterface
{
    /** @var SenderInterface */
    private $emailSender;

    /** @var InvoiceFileProviderInterface */
    private $invoiceFileProvider;

    /** @var TemporaryFilesystem */
    private $temporaryFilesystem;

    public function __construct(
        SenderInterface $emailSender,
        InvoiceFileProviderInterface $invoiceFileProvider
    ) {
        $this->emailSender = $emailSender;
        $this->invoiceFileProvider = $invoiceFileProvider;
        $this->temporaryFilesystem = new TemporaryFilesystem();
    }

    public function sendInvoiceEmail(
        InvoiceInterface $invoice,
        string $customerEmail
    ): void {
        $invoicePdf = $this->invoiceFileProvider->provide($invoice);

        // Since Sylius' Mailer does not support sending attachments which aren't real files
        // we have to simulate the file being on the local filesystem, so that we save the PDF,
        // run the callable and delete it when the callable is finished.
        $this->temporaryFilesystem->executeWithFile(
            $invoicePdf->filename(),
            $invoicePdf->content(),
            function (string $filepath) use ($invoice, $customerEmail): void {
                $this->emailSender->send(Emails::INVOICE_GENERATED, [$customerEmail], ['invoice' => $invoice], [$filepath]);
            }
        );
    }
}
