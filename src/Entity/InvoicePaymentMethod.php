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
}
