<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Email;

use Knp\Snappy\GeneratorInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\File\TemporaryFileSystemInterface;
use Symfony\Component\Templating\EngineInterface;

final class InvoiceEmailSender implements InvoiceEmailSenderInterface
{
    /** @var SenderInterface */
    private $emailSender;

    /** @var GeneratorInterface */
    private $pdfGenerator;

    /** @var EngineInterface */
    private $templatingEngine;

    /** @var TemporaryFileSystemInterface */
    private $temporaryFilePathGenerator;

    public function __construct(
        SenderInterface $emailSender,
        GeneratorInterface $pdfGenerator,
        EngineInterface $templatingEngine,
        TemporaryFileSystemInterface $temporaryFilePathGenerator
    ) {
        $this->emailSender = $emailSender;
        $this->pdfGenerator = $pdfGenerator;
        $this->templatingEngine = $templatingEngine;
        $this->temporaryFilePathGenerator = $temporaryFilePathGenerator;
    }

    public function sendInvoiceEmail(
        InvoiceInterface $invoice,
        string $customerEmail
    ): void {
        $pdfFileContent = $this->pdfGenerator->getOutputFromHtml(
            $this->templatingEngine->render('@SyliusInvoicingPlugin/Resources/views/Invoice/Download/pdf.html.twig', [
                'invoice' => $invoice
        ]));

        $filePath = $this->preparePdfFilePath('invoice-%s.pdf', $invoice->id());

        $this->temporaryFilePathGenerator->create(
            $pdfFileContent,
            $filePath
        );

        try {
            $this->emailSender->send(Emails::INVOICE_GENERATED, [$customerEmail], ['invoice' => $invoice], [$filePath]);
        } finally {
            $this->temporaryFilePathGenerator->removeFile($filePath);
        }
    }

    private function preparePdfFilePath(string $filePathPattern, string...$filePathParameters): string
    {
        return sys_get_temp_dir() . '/' . vsprintf($filePathPattern, $filePathParameters);
    }
}
