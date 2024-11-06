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
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingData;
use Sylius\InvoicingPlugin\Factory\InvoiceShopBillingDataFactoryInterface;

class InvoiceShopBillingDataFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(InvoiceShopBillingData::class);
    }

    function it_implements_invoice_shop_billing_data_factory_interface(): void
    {
        $this->shouldImplement(InvoiceShopBillingDataFactoryInterface::class);
    }

    function it_creates_invoice_shop_billing_data_from_channel_with_null_shop_billing_data(ChannelInterface $channel): void
    {
        $channel->getShopBillingData()->willReturn(null);

        $invoiceShopBillingData = new InvoiceShopBillingData();

        $this->createFromChannel($channel)->shouldBeLike($invoiceShopBillingData);
    }

    function it_creates_invoice_shop_billing_data_from_channel_with_shop_billing_data(
        ChannelInterface $channel,
        ShopBillingDataInterface $shopBillingData,
    ): void {
        $channel->getShopBillingData()->willReturn($shopBillingData);

        $shopBillingData->getCompany()->willReturn('Shelby Company Limited');
        $shopBillingData->getTaxId()->willReturn('56-60123456');
        $shopBillingData->getCountryCode()->willReturn('US');
        $shopBillingData->getStreet()->willReturn('Fremont Street');
        $shopBillingData->getCity()->willReturn('Las Vegas');
        $shopBillingData->getPostcode()->willReturn('000001');

        $invoiceShopBillingData = new InvoiceShopBillingData();
        $invoiceShopBillingData->setCompany('Shelby Company Limited');
        $invoiceShopBillingData->setTaxId('56-60123456');
        $invoiceShopBillingData->setCountryCode('US');
        $invoiceShopBillingData->setStreet('Fremont Street');
        $invoiceShopBillingData->setCity('Las Vegas');
        $invoiceShopBillingData->setPostcode('000001');

        $this->createFromChannel($channel)->shouldBeLike($invoiceShopBillingData);
    }

    function it_creates_invoice_shop_billing_data_from_shop_billing_data(ShopBillingDataInterface $shopBillingData): void
    {
        $shopBillingData->getCompany()->willReturn('Shelby Company Limited');
        $shopBillingData->getTaxId()->willReturn('56-60123456');
        $shopBillingData->getCountryCode()->willReturn('US');
        $shopBillingData->getStreet()->willReturn('Fremont Street');
        $shopBillingData->getCity()->willReturn('Las Vegas');
        $shopBillingData->getPostcode()->willReturn('000001');

        $invoiceShopBillingData = new InvoiceShopBillingData();
        $invoiceShopBillingData->setCompany('Shelby Company Limited');
        $invoiceShopBillingData->setTaxId('56-60123456');
        $invoiceShopBillingData->setCountryCode('US');
        $invoiceShopBillingData->setStreet('Fremont Street');
        $invoiceShopBillingData->setCity('Las Vegas');
        $invoiceShopBillingData->setPostcode('000001');

        $this->createFromShopBillingData($shopBillingData)->shouldBeLike($invoiceShopBillingData);
    }
}
