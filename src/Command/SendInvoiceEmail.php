<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Command;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

final class SendInvoiceEmail extends Command
{
    use PayloadTrait;

    public function __construct(string $orderNumber)
    {
        $this->init();
        $this->setPayload(['orderNumber' => $orderNumber]);
    }

    public function orderNumber(): string
    {
        return $this->payload()['orderNumber'];
    }
}
