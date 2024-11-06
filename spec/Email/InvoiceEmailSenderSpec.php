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

namespace spec\Sylius\InvoicingPlugin\Email;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\InvoicingPlugin\Email\Emails;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Sylius\InvoicingPlugin\Provider\InvoiceFileProviderInterface;

final class InvoiceEmailSenderSpec extends ObjectBehavior
{
    function let(
        SenderInterface $sender,
        InvoiceFileProviderInterface $invoiceFileProvider,
    ): void {
        $this->beConstructedWith($sender, $invoiceFileProvider);
    }

    function it_implements_invoice_email_sender_interface(): void
    {
        $this->shouldImplement(InvoiceEmailSenderInterface::class);
    }

    function it_sends_an_invoice_to_a_given_email_address(
        InvoiceInterface $invoice,
        SenderInterface $sender,
        InvoiceFileProviderInterface $invoiceFileProvider,
    ): void {
        $invoicePdf = new InvoicePdf('invoice.pdf', 'CONTENT');
        $invoicePdf->setFullPath('/path/to/invoices/invoice.pdf');

        $invoiceFileProvider->provide($invoice)->willReturn($invoicePdf);

        $sender->send(
            Emails::INVOICE_GENERATED,
            ['sylius@example.com'],
            ['invoice' => $invoice],
            ['/path/to/invoices/invoice.pdf'],
        )->shouldBeCalled();

        $this->sendInvoiceEmail($invoice, 'sylius@example.com');
    }

    function it_sends_an_invoice_without_attachment_to_a_given_email_address(
        InvoiceInterface $invoice,
        SenderInterface $sender,
        InvoiceFileProviderInterface $invoiceFileProvider,
    ): void {
        $this->beConstructedWith($sender, $invoiceFileProvider, false);

        $invoiceFileProvider->provide($invoice)->shouldNotBeCalled();

        $sender->send(Emails::INVOICE_GENERATED, ['sylius@example.com'], ['invoice' => $invoice])->shouldBeCalled();

        $this->sendInvoiceEmail($invoice, 'sylius@example.com');
    }
}
