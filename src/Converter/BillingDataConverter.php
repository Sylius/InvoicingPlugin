<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Converter;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\InvoicingPlugin\Entity\BillingData;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;

final class BillingDataConverter implements BillingDataConverterInterface
{
    public function convert(AddressInterface $billingAddress): BillingDataInterface
    {
        return new BillingData(
            $billingAddress->getFirstName(),
            $billingAddress->getLastName(),
            $billingAddress->getCountryCode(),
            $billingAddress->getStreet(),
            $billingAddress->getCity(),
            $billingAddress->getPostcode(),
            $billingAddress->getProvinceCode(),
            $billingAddress->getProvinceName(),
            $billingAddress->getCompany()
        );
    }
}
