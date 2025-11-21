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

namespace Tests\Sylius\InvoicingPlugin\Unit\Entity;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\InvoicingPlugin\Entity\BillingData;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;

final class BillingDataTest extends TestCase
{
    private BillingData $billingData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->billingData = new BillingData(
            'John',
            'Doe',
            'US',
            'Foo Street 3/44',
            'New York',
            '24154',
            'IE',
            'Utah',
            'Company Ltd.',
        );
    }

    #[Test]
    public function it_implements_billing_data_interface(): void
    {
        self::assertInstanceOf(BillingDataInterface::class, $this->billingData);
    }

    #[Test]
    public function it_implements_resource_interface(): void
    {
        self::assertInstanceOf(ResourceInterface::class, $this->billingData);
    }

    #[Test]
    public function it_has_proper_billing_data(): void
    {
        self::assertSame('John', $this->billingData->firstName());
        $this->assertSame('Doe', $this->billingData->lastName());
        $this->assertSame('US', $this->billingData->countryCode());
        $this->assertSame('Foo Street 3/44', $this->billingData->street());
        $this->assertSame('New York', $this->billingData->city());
        $this->assertSame('24154', $this->billingData->postcode());
        $this->assertSame('IE', $this->billingData->provinceCode());
        $this->assertSame('Utah', $this->billingData->provinceName());
        $this->assertSame('Company Ltd.', $this->billingData->company());
    }
}
