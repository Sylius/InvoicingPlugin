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

namespace spec\Sylius\InvoicingPlugin\Email;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\InvoicingPlugin\Email\Emails;
use Sylius\InvoicingPlugin\Email\InvoiceEmailSenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Sylius\InvoicingPlugin\Provider\InvoiceFilePathProviderInterface;

final class InvoiceEmailSenderSpec extends ObjectBehavior
{
    public function let(
        SenderInterface $sender,
        InvoiceFilePathProviderInterface $invoiceFilePathProvider
    ): void {
        $this->beConstructedWith($sender, $invoiceFilePathProvider);
    }

    public function it_implements_invoice_email_sender_interface(): void
    {
        $this->shouldImplement(InvoiceEmailSenderInterface::class);
    }

    public function it_sends_an_invoice_to_a_given_email_address(
        InvoiceInterface $invoice,
        SenderInterface $sender,
        InvoiceFilePathProviderInterface $invoiceFilePathProvider
    ): void {
        $invoiceFilePathProvider->provide($invoice)->willReturn('/path/to/invoice.pdf');

        $sender->send(
            Emails::INVOICE_GENERATED,
            ['sylius@example.com'],
            ['invoice' => $invoice],
            ['/path/to/invoice.pdf']
        )->shouldBeCalled();

        $this->sendInvoiceEmail($invoice, 'sylius@example.com');
    }
}
