<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Email;

use League\Flysystem\FilesystemInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Filesystem\TemporaryFilesystem;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;

final class InvoiceEmailSender implements InvoiceEmailSenderInterface
{
    /** @var SenderInterface */
    private $emailSender;

    /** @var InvoicePdfFileGeneratorInterface */
    private $invoicePdfFileGenerator;

    /** @var TemporaryFilesystem */
    private $temporaryFilesystem;

    /** @var FilesystemInterface */
    private $invoiceFilesystem;

    public function __construct(
        SenderInterface $emailSender,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        FilesystemInterface $invoiceFilesystem
    ) {
        $this->emailSender = $emailSender;
        $this->invoicePdfFileGenerator = $invoicePdfFileGenerator;
        $this->temporaryFilesystem = new TemporaryFilesystem();
        $this->invoiceFilesystem = $invoiceFilesystem;
    }

    public function sendInvoiceEmail(
        InvoiceInterface $invoice,
        string $customerEmail
    ): void {
        $pdfInvoiceFilename = $this->invoicePdfFileGenerator->buildFilenameForInvoice($invoice);

        if (!$this->invoiceFilesystem->has($pdfInvoiceFilename)) {
            $pdfInvoice = $this->invoicePdfFileGenerator->generate($invoice);

            $this->invoiceFilesystem->put(
                $pdfInvoice->filename(),
                $pdfInvoice->content()
            );
        }

        $pdfInvoiceFile = $this->invoiceFilesystem->read($pdfInvoiceFilename);

        if ($pdfInvoiceFile === false) {
            return;
        }

        // Since Sylius' Mailer does not support sending attachments which aren't real files
        // we have to simulate the file being on the local filesystem, so that we save the PDF,
        // run the callable and delete it when the callable is finished.
        $this->temporaryFilesystem->executeWithFile(
            $pdfInvoiceFilename,
            $pdfInvoiceFile,
            function (string $filepath) use ($invoice, $customerEmail): void {
                $this->emailSender->send(Emails::INVOICE_GENERATED, [$customerEmail], ['invoice' => $invoice], [$filepath]);
            }
        );
    }
}
