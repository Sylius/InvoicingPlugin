<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

class TaxItem implements TaxItemInterface, ResourceInterface
{
    /** @var string */
    protected $id;

    /** @var InvoiceInterface */
    protected $invoice;

    /** @var string */
    protected $label;

    /** @var int */
    protected $amount;

    /** @var float */
    protected $taxRate;

    public function __construct(string $label, int $amount, float $taxRate)
    {
        $this->label = $label;
        $this->amount = $amount;
        $this->taxRate = $taxRate;
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

    public function taxRate(): float
    {
        return $this->taxRate;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @param int $amount
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param float $taxRate
     */
    public function setTaxRate(float $taxRate): void
    {
        $this->taxRate = $taxRate;
    }
}
