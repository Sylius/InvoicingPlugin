<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Sylius\Component\Core\Model\AddressInterface;

trait ShopBillingDataTrait
{
    /**
     * @Column(type="string", nullable=true)
     *
     * @var string
     */
    private $shopName;

    /**
     * @Column(type="string", nullable=true)
     *
     * @var string
     */
    private $taxId;

    /**
     * @OneToOne(targetEntity="Sylius\Component\Addressing\Model\AddressInterface")
     * @JoinColumn(name="billing_address_id", referencedColumnName="id")
     *
     * @var AddressInterface
     */
    private $billingAddress;

    public function getShopName(): ?string
    {
        return $this->shopName;
    }

    public function setShopName(?string $shopName): void
    {
        $this->shopName = $shopName;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function setTaxId(?string $taxId): void
    {
        $this->taxId = $taxId;
    }

    public function getBillingAddress(): ?AddressInterface
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(?AddressInterface $billingAddress): void
    {
        $this->billingAddress = $billingAddress;
    }
}
