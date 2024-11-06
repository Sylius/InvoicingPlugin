<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Factory;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;
use Sylius\InvoicingPlugin\Entity\BillingData;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Webmozart\Assert\Assert;

final class BillingDataFactory implements BillingDataFactoryInterface
{
    public function __construct(private readonly string $className)
    {
        if (!is_a($className, BillingData::class, true)) {
            throw new \DomainException(sprintf(
                'This factory requires %s or its descend to be used as billing data resource',
                BillingData::class,
            ));
        }
    }

    public function createNew()
    {
        throw new UnsupportedMethodException('createNew');
    }

    public function createFromAddress(AddressInterface $address): BillingDataInterface
    {
        /** @var BillingDataInterface $billingData */
        $billingData = new $this->className(
            $address->getFirstName(),
            $address->getLastName(),
            $address->getCountryCode(),
            $address->getStreet(),
            $address->getCity(),
            $address->getPostcode(),
            $address->getProvinceCode(),
            $address->getProvinceName(),
            $address->getCompany(),
        );

        Assert::isInstanceOf($billingData, BillingDataInterface::class);

        return $billingData;
    }
}
