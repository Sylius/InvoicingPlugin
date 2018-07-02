<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;

trait ShopBillingDataTrait
{
    /**
     * @Column(type="string", nullable=true)
     *
     * @var string
     */
    private $taxId;

    /**
     * @OneToOne(targetEntity="Sylius\InvoicingPlugin\Entity\ShopBillingData", cascade={"persist"})
     * @JoinColumn(name="billing_data_id", referencedColumnName="id")
     *
     * @var ShopBillingDataInterface
     */
    private $billingData;

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function setTaxId(?string $taxId): void
    {
        $this->taxId = $taxId;
    }

    public function getBillingData(): ?ShopBillingDataInterface
    {
        return $this->billingData;
    }

    public function setBillingData(?ShopBillingDataInterface $billingData): void
    {
        $this->billingData = $billingData;
    }
}
