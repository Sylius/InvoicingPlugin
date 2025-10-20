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

namespace Tests\Sylius\InvoicingPlugin\Unit\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;
use Sylius\InvoicingPlugin\Entity\BillingData;
use Sylius\InvoicingPlugin\Factory\BillingDataFactory;
use Sylius\InvoicingPlugin\Factory\BillingDataFactoryInterface;

final class BillingDataFactoryTest extends TestCase
{
    private BillingDataFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new BillingDataFactory(BillingData::class);
    }

    #[Test]
    public function it_implements_billing_data_factory_interface(): void
    {
        self::assertInstanceOf(BillingDataFactoryInterface::class, $this->factory);
    }

    #[Test]
    public function it_does_not_allow_to_create_empty_data(): void
    {
        $this->expectException(UnsupportedMethodException::class);

        $this->factory->createNew();
    }

    #[Test]
    public function it_allows_only_for_injection_of_fqcn_that_are_billing_data_or_its_descendants(): void
    {
        $this->expectException(\DomainException::class);

        new BillingDataFactory(\stdClass::class);
    }

    #[Test]
    public function it_creates_billing_data_from_address(): void
    {
        $address = $this->createMock(AddressInterface::class);

        $address->expects(self::once())->method('getCountryCode')->willReturn('US');
        $address->expects(self::once())->method('getCity')->willReturn('Las Vegas');
        $address->expects(self::once())->method('getPostcode')->willReturn('000001');
        $address->expects(self::once())->method('getStreet')->willReturn('Fremont Street');
        $address->expects(self::once())->method('getProvinceCode')->willReturn('sample_province_code');
        $address->expects(self::once())->method('getProvinceName')->willReturn('sample_province_name');
        $address->expects(self::once())->method('getFirstName')->willReturn('Thomas');
        $address->expects(self::once())->method('getLastName')->willReturn('Shelby');
        $address->expects(self::once())->method('getCompany')->willReturn('Shelby Company Limited');

        $result = $this->factory->createFromAddress($address);
        $expected = new BillingData(
            'Thomas',
            'Shelby',
            'US',
            'Fremont Street',
            'Las Vegas',
            '000001',
            'sample_province_code',
            'sample_province_name',
            'Shelby Company Limited',
        );

        self::assertEquals($expected, $result);
    }
}
