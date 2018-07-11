<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

/** @final */
class Invoice implements InvoiceInterface, ResourceInterface
{
    /** @var string */
    private $id;

    /** @var string */
    private $orderNumber;

    /** @var \DateTimeInterface */
    private $issuedAt;

    /** @var BillingDataInterface */
    private $billingData;

    /** @var string */
    private $currencyCode;

    /** @var int */
    private $total;

    /** @var Collection|LineItemInterface[] */
    private $lineItems;

    /** @var Collection|TaxItemInterface[] */
    private $taxItems;

    public function __construct(
        string $id,
        string $orderNumber,
        \DateTimeInterface $issuedAt,
        BillingDataInterface $billingData,
        string $currencyCode,
        int $total,
        Collection $lineItems,
        Collection $taxItems
    ) {
        $this->id = $id;
        $this->orderNumber = $orderNumber;
        $this->issuedAt = clone $issuedAt;
        $this->billingData = $billingData;
        $this->currencyCode = $currencyCode;
        $this->total = $total;
        $this->lineItems = $lineItems;
        $this->taxItems = $taxItems;

        /** @var LineItemInterface $lineItem */
        foreach ($lineItems as $lineItem) {
            $lineItem->setInvoice($this);
        }

        /** @var TaxItemInterface $taxItem */
        foreach ($taxItems as $taxItem) {
            $taxItem->setInvoice($this);
        }
    }

    public function getId(): string
    {
        return $this->id();
    }

    public function id(): string
    {
        return $this->id;
    }

    public function orderNumber(): string
    {
        return $this->orderNumber;
    }

    public function issuedAt(): \DateTimeInterface
    {
        return clone $this->issuedAt;
    }

    public function billingData(): BillingDataInterface
    {
        return $this->billingData;
    }

    public function currencyCode(): string
    {
        return $this->currencyCode;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function lineItems(): Collection
    {
        return $this->lineItems;
    }

    public function taxItems(): Collection
    {
        return $this->taxItems;
    }

    public function subtotal(): int
    {
        $subtotal = 0;

        /** @var LineItemInterface $lineItem */
        foreach ($this->lineItems as $lineItem) {
            $subtotal += $lineItem->subtotal();
        }

        return $subtotal;
    }
}
