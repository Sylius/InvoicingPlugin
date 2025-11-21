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
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingData;
use Sylius\InvoicingPlugin\Factory\InvoiceShopBillingDataFactory;
use Sylius\InvoicingPlugin\Factory\InvoiceShopBillingDataFactoryInterface;

final class InvoiceShopBillingDataFactoryTest extends TestCase
{
    private InvoiceShopBillingDataFactory $invoiceShopBillingDataFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceShopBillingDataFactory = new InvoiceShopBillingDataFactory(InvoiceShopBillingData::class);
    }

    #[Test]
    public function it_implements_invoice_shop_billing_data_factory_interface(): void
    {
        self::assertInstanceOf(InvoiceShopBillingDataFactoryInterface::class, $this->invoiceShopBillingDataFactory);
    }

    #[Test]
    public function it_creates_invoice_shop_billing_data_from_channel_with_null_shop_billing_data(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getShopBillingData')->willReturn(null);

        $expected = new InvoiceShopBillingData();

        $result = $this->invoiceShopBillingDataFactory->createFromChannel($channel);

        self::assertEquals($expected, $result);
    }

    #[Test]
    public function it_creates_invoice_shop_billing_data_from_channel_with_shop_billing_data(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $shopBillingData = $this->createMock(ShopBillingDataInterface::class);

        $channel->method('getShopBillingData')->willReturn($shopBillingData);

        $shopBillingData->method('getCompany')->willReturn('Shelby Company Limited');
        $shopBillingData->method('getTaxId')->willReturn('56-60123456');
        $shopBillingData->method('getCountryCode')->willReturn('US');
        $shopBillingData->method('getStreet')->willReturn('Fremont Street');
        $shopBillingData->method('getCity')->willReturn('Las Vegas');
        $shopBillingData->method('getPostcode')->willReturn('000001');

        $expected = new InvoiceShopBillingData();
        $expected->setCompany('Shelby Company Limited');
        $expected->setTaxId('56-60123456');
        $expected->setCountryCode('US');
        $expected->setStreet('Fremont Street');
        $expected->setCity('Las Vegas');
        $expected->setPostcode('000001');

        $result = $this->invoiceShopBillingDataFactory->createFromChannel($channel);

        self::assertEquals($expected, $result);
    }

    #[Test]
    public function it_creates_invoice_shop_billing_data_from_shop_billing_data(): void
    {
        $shopBillingData = $this->createMock(ShopBillingDataInterface::class);

        $shopBillingData->method('getCompany')->willReturn('Shelby Company Limited');
        $shopBillingData->method('getTaxId')->willReturn('56-60123456');
        $shopBillingData->method('getCountryCode')->willReturn('US');
        $shopBillingData->method('getStreet')->willReturn('Fremont Street');
        $shopBillingData->method('getCity')->willReturn('Las Vegas');
        $shopBillingData->method('getPostcode')->willReturn('000001');

        $expected = new InvoiceShopBillingData();
        $expected->setCompany('Shelby Company Limited');
        $expected->setTaxId('56-60123456');
        $expected->setCountryCode('US');
        $expected->setStreet('Fremont Street');
        $expected->setCity('Las Vegas');
        $expected->setPostcode('000001');

        $result = $this->invoiceShopBillingDataFactory->createFromShopBillingData($shopBillingData);

        self::assertEquals($expected, $result);
    }
}
