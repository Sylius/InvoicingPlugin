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

    public function getAccountingCode(): ?string;

    public function setAccountingCode(?string $accountingCode): void;

    public function getAccountingNumber(): ?string;

    public function setAccountingNumber(?string $accountingNumber): void;
}
