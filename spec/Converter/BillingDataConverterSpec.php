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

namespace spec\Sylius\InvoicingPlugin\Converter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\InvoicingPlugin\Converter\BillingDataConverterInterface;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Sylius\InvoicingPlugin\Factory\BillingDataFactoryInterface;

final class BillingDataConverterSpec extends ObjectBehavior
{
    function let(BillingDataFactoryInterface $billingDataFactory): void
    {
        $this->beConstructedWith($billingDataFactory);
    }

    function it_implements_billing_data_converter_interface(): void
    {
        $this->shouldImplement(BillingDataConverterInterface::class);
    }

    function it_converts_address_to_billing_data(
        AddressInterface $address,
        BillingDataFactoryInterface $billingDataFactory,
        BillingDataInterface $billingData
    ): void {
        $billingDataFactory->createFromAddress($address)->willReturn($billingData);

        $this->convert($address)->shouldReturn($billingData);
    }
}
