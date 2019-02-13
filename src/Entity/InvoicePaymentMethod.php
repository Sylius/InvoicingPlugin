<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

class InvoicePaymentMethod implements InvoicePaymentMethodInterface
{
    /** @var string|null */
    protected $code;

    /** @var string|null */
    protected $name;

    /** @var string|null */
    protected $instructions;

    /** @var string|null */
    protected $accountingCode;

    /** @var string|null */
    protected $accountingNumber;

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getInstructions(): ?string
    {
        return $this->instructions;
    }

    public function setInstructions(?string $instructions): void
    {
        $this->instructions = $instructions;
    }

    public function getAccountingCode(): ?string
    {
        return $this->accountingCode;
    }

    public function setAccountingCode(?string $accountingCode): void
    {
        $this->accountingCode = $accountingCode;
    }

    public function getAccountingNumber(): ?string
    {
        return $this->accountingNumber;
    }

    public function setAccountingNumber(?string $accountingNumber): void
    {
        $this->accountingNumber = $accountingNumber;
    }
}
