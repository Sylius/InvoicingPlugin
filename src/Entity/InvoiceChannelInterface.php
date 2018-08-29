<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

interface InvoiceChannelInterface
{
    public function getCode(): string;

    public function getName(): string;
}
