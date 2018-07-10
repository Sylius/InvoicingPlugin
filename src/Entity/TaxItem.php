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
    private $name;

    /** @var float */
    private $rate;

    /** @var int */
    private $amount;

    public function __construct(string $name, float $rate, int $amount)
    {
        $this->name = $name;
        $this->rate = $rate;
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

    public function name(): string
    {
        return $this->name;
    }

    public function rate(): float
    {
        return $this->rate;
    }

    public function amount(): int
    {
        return $this->amount;
    }
}
