<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Email;

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

    public function sendInvoiceEmail(InvoiceInterface $invoice, string $customerEmail): void
    {
        $this->emailSender->send('Invoice', [$customerEmail], $invoice);
    }
}
