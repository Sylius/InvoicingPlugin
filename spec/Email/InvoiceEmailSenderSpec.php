<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Email;

use Knp\Snappy\GeneratorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\InvoicingPlugin\Email\Emails;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSender;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\File\TemporaryFileSystemInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\TemplateReferenceInterface;

final class InvoiceEmailSenderSpec extends ObjectBehavior
{
    public function let(
        SenderInterface $sender,
        TemporaryFileSystemInterface $temporaryFilePathGenerator,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator
    ): void {
        $this->beConstructedWith($sender, $temporaryFilePathGenerator, $invoicePdfFileGenerator);
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
        InvoiceInterface $invoice,
        SenderInterface $sender,
        TemporaryFileSystemInterface $temporaryFilePathGenerator,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator
    ): void {
        $invoicePdf = new InvoicePdf('invoice.pdf', 'invoice_pdf_content');

        $invoice->id()->willReturn('0000001');

        $invoicePdfFileGenerator->generate('0000001')->willReturn($invoicePdf);

        $temporaryFilePathGenerator->create('invoice_pdf_content', 'invoice.pdf')->shouldBeCalled();

        $sender->send(
            Emails::INVOICE_GENERATED, ['sylius@example.com'], ['invoice' => $invoice], ['invoice.pdf']
        )->shouldBeCalled();

        $temporaryFilePathGenerator->removeFile('invoice.pdf')->shouldBeCalled();

        $this->sendInvoiceEmail($invoice, 'sylius@example.com');
    }
}
