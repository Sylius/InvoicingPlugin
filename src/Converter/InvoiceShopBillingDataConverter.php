<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Converter;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingData;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;

final class InvoiceShopBillingDataConverter implements InvoiceShopBillingDataConverterInterface
{
    public function convert(ChannelInterface $channel): InvoiceShopBillingDataInterface
    {
        /** @var ShopBillingDataInterface $shopBillingData */
        $shopBillingData = $channel->getShopBillingData();

        $invoiceShopBillingData = new InvoiceShopBillingData();

        if (null === $shopBillingData) {
            return $invoiceShopBillingData;
        }

        $invoiceShopBillingData->setCompany($shopBillingData->getCompany());
        $invoiceShopBillingData->setTaxId($shopBillingData->getTaxId());
        $invoiceShopBillingData->setCountryCode($shopBillingData->getCountryCode());
        $invoiceShopBillingData->setStreet($shopBillingData->getStreet());
        $invoiceShopBillingData->setCity($shopBillingData->getCity());
        $invoiceShopBillingData->setPostcode($shopBillingData->getPostcode());

        return $invoiceShopBillingData;
    }
}
