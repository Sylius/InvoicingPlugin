<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Converter;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;

interface BillingDataConverterInterface
{
    public function convert(AddressInterface $billingAddress): BillingDataInterface;
}
