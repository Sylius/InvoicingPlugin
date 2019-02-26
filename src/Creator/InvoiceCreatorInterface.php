<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Creator;

interface InvoiceCreatorInterface
{
    public function __invoke(string $orderNumber, \DateTimeInterface $dateTime): void;
}
