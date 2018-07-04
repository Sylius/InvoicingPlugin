<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Email;

use Knp\Snappy\GeneratorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\InvoicingPlugin\Email\Emails;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSender;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\File\TemporaryFileSystemInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

final class InvoiceEmailSenderSpec extends ObjectBehavior
{
    public function let(
        SenderInterface $sender,
        GeneratorInterface $pdfGenerator,
        EngineInterface $templatingEngine,
        TemporaryFileSystemInterface $temporaryFilePathGenerator
    ) : void {
        $this->beConstructedWith($sender, $pdfGenerator, $templatingEngine, $temporaryFilePathGenerator);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(InvoiceEmailSender::class);
    }

    public function it_implements_invoice_email_sender_interface(): void
    {
        $this->shouldImplement(InvoiceEmailSenderInterface::class);
    }

    public function it_sends_an_invoice_to_a_given_email_address(
        EngineInterface $templatingEngine,
        GeneratorInterface $pdfGenerator,
        InvoiceInterface $invoice,
        SenderInterface $sender,
        TemporaryFileSystemInterface $temporaryFilePathGenerator,
        TemplateReferenceInterface $templateReference
    ): void {
        $invoice->id()->willReturn('0000001');
        $filePath = sys_get_temp_dir() . '/' . 'invoice-0000001.pdf';

        $templatingEngine->render(
            '@SyliusInvoicingPlugin/Resources/views/Invoice/Download/pdf.html.twig', [
                'invoice' => $invoice
            ]
        )->willReturn($templateReference);

        $pdfGenerator->getOutputFromHtml($templateReference)->willReturn('test');

        $temporaryFilePathGenerator->create('test', $filePath)->shouldBeCalled();

        $sender->send(
            Emails::INVOICE_GENERATED, ['sylius@example.com'], ['invoice' => $invoice], [$filePath]
        )->shouldBeCalled();

        $temporaryFilePathGenerator->removeFile($filePath)->shouldBeCalled();

        $this->sendInvoiceEmail($invoice, 'sylius@example.com');
    }
}
