<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Converter;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Sylius\InvoicingPlugin\Factory\BillingDataFactoryInterface;

final class BillingDataConverter implements BillingDataConverterInterface
{
    private BillingDataFactoryInterface $billingDataFactory;

    public function __construct(BillingDataFactoryInterface $billingDataFactory)
    {
        $this->billingDataFactory = $billingDataFactory;
    }

    public function convert(AddressInterface $billingAddress): BillingDataInterface
    {
        return $this->billingDataFactory->createFromAddress($billingAddress);
    }
}
