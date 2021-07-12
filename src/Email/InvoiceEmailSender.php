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
use Sylius\InvoicingPlugin\Provider\InvoiceFilePathProviderInterface;

final class InvoiceEmailSender implements InvoiceEmailSenderInterface
{
    /** @var SenderInterface */
    private $emailSender;

    /** @var InvoiceFilePathProviderInterface */
    private $invoiceFilePathProvider;

    public function __construct(
        SenderInterface $emailSender,
        InvoiceFilePathProviderInterface $invoiceFilePathProvider
    ) {
        $this->emailSender = $emailSender;
        $this->invoiceFilePathProvider = $invoiceFilePathProvider;
    }

    public function sendInvoiceEmail(
        InvoiceInterface $invoice,
        string $customerEmail
    ): void {
        $filepath = $this->invoiceFilePathProvider->provide($invoice);

        $this->emailSender->send(Emails::INVOICE_GENERATED, [$customerEmail], ['invoice' => $invoice], [$filepath]);
    }
}
