<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Email;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

interface InvoiceEmailSenderInterface
{
    public function sendInvoiceEmail(InvoiceInterface $invoice, ChannelInterface $channel, string $customerEmail): void;
}
