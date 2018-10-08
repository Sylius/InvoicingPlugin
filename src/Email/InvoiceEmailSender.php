<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Email;

use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\File\TemporaryFileSystemInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;

final class InvoiceEmailSender implements InvoiceEmailSenderInterface
{
    /** @var SenderInterface */
    private $emailSender;

    /** @var TemporaryFileSystemInterface */
    private $temporaryFilePathGenerator;

    /** @var InvoicePdfFileGeneratorInterface */
    private $invoicePdfFileGenerator;

    public function __construct(
        SenderInterface $emailSender,
        TemporaryFileSystemInterface $temporaryFilePathGenerator,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator
    ) {
        $this->emailSender = $emailSender;
        $this->temporaryFilePathGenerator = $temporaryFilePathGenerator;
        $this->invoicePdfFileGenerator = $invoicePdfFileGenerator;
    }

    public function sendInvoiceEmail(
        InvoiceInterface $invoice,
        string $customerEmail
    ): void {
        $invoicePdfFile = $this->invoicePdfFileGenerator->generate($invoice->id());

        $fileName = $invoicePdfFile->filename();

        $this->temporaryFilePathGenerator->create($invoicePdfFile->content(), $fileName);

        try {
            $this->emailSender->send(Emails::INVOICE_GENERATED, [$customerEmail], ['invoice' => $invoice], [$fileName]);
        } finally {
            $this->temporaryFilePathGenerator->removeFile($fileName);
        }
    }

    private function preparePdfFilePath(string $filePathPattern, string ...$filePathParameters): string
    {
        return sys_get_temp_dir() . '/' . vsprintf($filePathPattern, $filePathParameters);
    }
}
