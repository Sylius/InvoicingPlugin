<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

final class InvoicePaymentMethod implements InvoicePaymentMethodInterface
{
    /** @var string|null */
    protected $name;

    /** @var string|null */
    protected $instructions;

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
