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
    private $taxTotal;

    /** @var int */
    private $total;

    /** @var Collection|LineItemInterface[] */
    private $lineItems;

    public function __construct(
        string $id,
        string $orderNumber,
        \DateTimeInterface $issuedAt,
        BillingDataInterface $billingData,
        string $currencyCode,
        int $taxTotal,
        int $total,
        Collection $lineItems
    ) {
        $this->id = $id;
        $this->orderNumber = $orderNumber;
        $this->issuedAt = clone $issuedAt;
        $this->billingData = $billingData;
        $this->currencyCode = $currencyCode;
        $this->taxTotal = $taxTotal;
        $this->total = $total;
        $this->lineItems = $lineItems;

        /** @var LineItemInterface $lineItem */
        foreach ($lineItems as $lineItem) {
            $lineItem->setInvoice($this);
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

    public function taxTotal(): int
    {
        return $this->taxTotal;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function lineItems(): Collection
    {
        return $this->lineItems;
    }
}
