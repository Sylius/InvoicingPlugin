<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

/** @final */
class TaxItem implements TaxItemInterface, ResourceInterface
{
    /** @var string */
    private $id;

    /** @var InvoiceInterface */
    private $invoice;

    /** @var string */
    private $label;

    /** @var int */
    private $amount;

    public function __construct(string $label, int $amount)
    {
        $this->label = $label;
        $this->amount = $amount;
    }

    public function getId(): string
    {
        return $this->id();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function invoice(): InvoiceInterface
    {
        return $this->invoice;
    }

    public function setInvoice(InvoiceInterface $invoice): void
    {
        $this->invoice = $invoice;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function amount(): int
    {
        return $this->amount;
    }
}
