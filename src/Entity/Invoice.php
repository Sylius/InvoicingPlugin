<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;

class Invoice implements InvoiceInterface, ResourceInterface
{
    /** @var string */
    protected $id;

    /** @var string */
    protected $number;

    /** @var string */
    protected $orderNumber;

    /** @var \DateTimeInterface */
    protected $issuedAt;

    /** @var BillingDataInterface */
    protected $billingData;

    /** @var string */
    protected $currencyCode;

    /** @var string */
    protected $localeCode;

    /** @var int */
    private $taxTotal;

    /** @var int */
    protected $total;

    /** @var Collection|LineItemInterface[] */
    protected $lineItems;

    /** @var Collection|TaxItemInterface[] */
    protected $taxItems;

    /** @var InvoiceChannelInterface */
    protected $channel;

    /** @var InvoiceShopBillingDataInterface */
    protected $shopBillingData;

    /** @var InvoicePaymentMethodInterface */
    protected $paymentMethod;

    /** @var \DateTimeInterface|null */
    protected $paymentDueDate;

    /** @var string|null */
    protected $paymentReference;

    /** @var string|null */
    protected $paymentCommitmentNumber;

    /** @var string|null */
    protected $orderCustomerReference;

    /** @var string|null */
    protected $promotionCouponUsed;

    public function __construct(
        string $id,
        string $number,
        string $orderNumber,
        \DateTimeInterface $issuedAt,
        BillingDataInterface $billingData,
        string $currencyCode,
        string $localeCode,
        int $taxTotal,
        int $total,
        Collection $lineItems,
        Collection $taxItems,
        InvoiceChannelInterface $channel,
        InvoiceShopBillingDataInterface $shopBillingData,
        InvoicePaymentMethodInterface $paymentMethod,
        ?\DateTimeInterface $paymentDueDate = null,
        ?string $paymentReference = null,
        ?string $paymentCommitmentNumber = null,
        ?string $orderCustomerReference = null,
        ?string $promotionCouponUsed = null
    ) {
        $this->id = $id;
        $this->number = $number;
        $this->orderNumber = $orderNumber;
        $this->issuedAt = clone $issuedAt;
        $this->billingData = $billingData;
        $this->currencyCode = $currencyCode;
        $this->localeCode = $localeCode;
        $this->taxTotal = $taxTotal;
        $this->total = $total;
        $this->lineItems = $lineItems;
        $this->taxItems = $taxItems;
        $this->channel = $channel;
        $this->shopBillingData = $shopBillingData;
        $this->paymentMethod = $paymentMethod;
        $this->paymentDueDate = $paymentDueDate;
        $this->paymentReference = $paymentReference;
        $this->paymentCommitmentNumber = $paymentCommitmentNumber;
        $this->orderCustomerReference = $orderCustomerReference;
        $this->promotionCouponUsed = $promotionCouponUsed;

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

    public function number(): string
    {
        return $this->number;
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

    public function localeCode(): string
    {
        return $this->localeCode;
    }

    public function taxTotal(): int
    {
        return $this->taxTotal;
    }

    public function setTaxTotal(int $taxTotal): void
    {
        $this->taxTotal = $taxTotal;
    }

    public function total(): int
    {
        return $this->total;
    }

    public function setTotal(int $total): void
    {
        $this->total = $total;
    }

    public function lineItems(): Collection
    {
        return $this->lineItems;
    }

    public function taxItems(): Collection
    {
        return $this->taxItems;
    }

    public function sortedTaxItems(): array
    {
        $taxItems = $this->taxItems()->toArray();

        usort($taxItems, function (?TaxItem $a, ?TaxItem $b) {
            if ($a === null && $b === null) return 0;
            if ($a === null) return 1;
            if ($b === null) return -1;
            return strnatcasecmp(strval($a->taxRate() ?? 0.0), strval($b->taxRate() ?? 0.0));
        });

        return $taxItems;
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

    public function remainingPromotionSubtotal(): int {
        if (0 === $remainingPromotionTotal = $this->remainingPromotionTotal()) {
            return 0;
        }

        $taxRate = 0.2;

        /** @var LineItemInterface $lineItem */
        foreach ($this->lineItems as $lineItem) {
            if ($lineItem->type() === LineItem::TYPE_PROMOTION && $lineItem->taxRate() !== null) {
                $taxRate = $lineItem->taxRate();
                break;
            }
        }

        return $remainingPromotionTotal / (1.0 + $taxRate);
    }

    public function remainingPromotionTotal(): int {
        $itemPromotionTotal = 0;
        $promotionTotal = 0;

        /** @var LineItemInterface $lineItem */
        foreach ($this->lineItems as $lineItem) {
            if ($lineItem->type() === LineItem::TYPE_PROMOTION) {
                $promotionTotal += $lineItem->total();
            } else {
                $itemPromotionTotal += $lineItem->promotionTotal();
            }
        }

        return ($promotionTotal - $itemPromotionTotal < 0) ? $promotionTotal - $itemPromotionTotal : 0;
    }

    public function channel(): InvoiceChannelInterface
    {
        return $this->channel;
    }

    public function shopBillingData(): InvoiceShopBillingDataInterface
    {
        return $this->shopBillingData;
    }

    public function paymentMethod(): InvoicePaymentMethodInterface
    {
        return $this->paymentMethod;
    }

    public function paymentDueDate(): ?\DateTimeInterface
    {
        return $this->paymentDueDate;
    }

    public function setPaymentDueDate(?\DateTimeInterface $paymentDueDate): void
    {
        $this->paymentDueDate = $paymentDueDate;
    }

    public function paymentReference(): ?string
    {
        return $this->paymentReference;
    }

    public function setPaymentReference(?string $paymentReference): void
    {
        $this->paymentReference = $paymentReference;
    }

    public function paymentCommitmentNumber(): ?string
    {
        return $this->paymentCommitmentNumber;
    }

    public function setPaymentCommitmentNumber(?string $paymentCommitmentNumber): void
    {
        $this->paymentCommitmentNumber = $paymentCommitmentNumber;
    }

    public function orderCustomerReference(): ?string
    {
        return $this->orderCustomerReference;
    }

    public function promotionCouponUsed(): ?string
    {
        return $this->promotionCouponUsed;
    }
}
