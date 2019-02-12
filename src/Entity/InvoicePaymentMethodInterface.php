<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

interface InvoicePaymentMethodInterface
{
    public function getCode(): ?string;

    public function setCode(?string $code): void;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getInstructions(): ?string;

    public function setInstructions(?string $instructions): void;
}
