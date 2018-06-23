<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Generator;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

interface InvoiceGeneratorInterface
{
    public function generateForOrder(OrderInterface $order, \DateTimeInterface $date): InvoiceInterface;
}
