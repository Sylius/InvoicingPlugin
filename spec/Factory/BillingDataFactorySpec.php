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

namespace spec\Sylius\InvoicingPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;
use Sylius\InvoicingPlugin\Entity\BillingData;
use Sylius\InvoicingPlugin\Factory\BillingDataFactoryInterface;

class BillingDataFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(BillingData::class);
    }

    function it_implements_billing_data_factory_interface(): void
    {
        $this->shouldImplement(BillingDataFactoryInterface::class);
    }

    function it_does_not_allow_to_create_empty_data(): void
    {
        $this->shouldThrow(UnsupportedMethodException::class)->during('createNew');
    }

    function it_allows_only_for_injection_of_fqcn_that_are_billing_data_or_its_descendants(AddressInterface $address): void
    {
        $this->beConstructedWith(\stdClass::class);

        $this->shouldThrow(\DomainException::class)->duringInstantiation();
    }

    function it_creates_billing_data_from_address(AddressInterface $address): void
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

        $this->createFromAddress($address)->shouldBeLike(new BillingData(
            'Thomas',
            'Shelby',
            'US',
            'Fremont Street',
            'Las Vegas',
            '000001',
            'sample_province_code',
            'sample_province_name',
            'Shelby Company Limited',
        ));
    }
}
