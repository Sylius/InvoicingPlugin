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
use Sylius\InvoicingPlugin\Entity\BillingData;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;

final class BillingDataConverterSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(
            BillingData::class
        );
    }

    function it_implements_billing_data_converter_interface(): void
    {
        $this->shouldImplement(BillingDataConverterInterface::class);
    }

    function it_converts_address_to_billing_data(AddressInterface $address): void
    {
        $address->getCountryCode()->willReturn('US');
        $address->getCity()->willReturn('Las Vegas');
        $address->getPostcode()->willReturn('000001');
        $address->getStreet()->willReturn('Fremont Street');
        $address->getProvinceCode()->willReturn('sample_province_code');
        $address->getProvinceName()->willReturn('sample_province_name');
        $address->getFirstName()->willReturn('Thomas');
        $address->getLastName()->willReturn('Shelby');
        $address->getCompany()->willReturn('Shelby Company Limited');

        $this->convert($address)->shouldReturnAnInstanceOf(BillingDataInterface::class);
    }
}
