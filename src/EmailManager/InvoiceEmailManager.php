<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EmailManager;

use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

final class InvoiceEmailManager implements InvoiceEmailManagerInterface
{
    /**
     * @var SenderInterface
     */
    private $emailSender;

    /**
     * @param SenderInterface $emailSender
     */
    public function __construct(SenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    public function sendInvoiceEmail(
        InvoiceInterface $invoice,
        PdfResponse $invoiceAttachment,
        string $customerEmail
    ): void {
        $this->emailSender->send(
            Emails::INVOICE_GENERATED,
            [$customerEmail],
            ['invoice' => $invoice],
            ['invoice' => $invoiceAttachment]
        );
    }
}
