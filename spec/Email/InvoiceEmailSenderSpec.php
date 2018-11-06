<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Email;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\InvoicingPlugin\Email\Emails;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;

final class InvoiceEmailSenderSpec extends ObjectBehavior
{
    public function let(
        SenderInterface $sender,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator
    ): void {
        $this->beConstructedWith($sender, $invoicePdfFileGenerator);
    }

    public function it_implements_invoice_email_sender_interface(): void
    {
        $this->shouldImplement(InvoiceEmailSenderInterface::class);
    }

    public function it_sends_an_invoice_to_a_given_email_address(
        InvoiceInterface $invoice,
        SenderInterface $sender,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator
    ): void {
        $invoicePdf = new InvoicePdf('invoice.pdf', 'invoice_pdf_content');

        $invoice->id()->willReturn('0000001');

        $invoicePdfFileGenerator->generate('0000001')->willReturn($invoicePdf);

        $sender->send(
            Emails::INVOICE_GENERATED,
            ['sylius@example.com'],
            ['invoice' => $invoice],
            Argument::that(function (array $argument): bool {
                return count($argument) === 1 && preg_match('/.+invoice\.pdf$/', $argument[0]) === 1;
            })
        )->shouldBeCalled();

        $this->sendInvoiceEmail($invoice, 'sylius@example.com');
    }
}
