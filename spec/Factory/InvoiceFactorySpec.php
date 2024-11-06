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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Sylius\InvoicingPlugin\Entity\Invoice;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingData;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;
use Sylius\InvoicingPlugin\Factory\InvoiceFactoryInterface;

class InvoiceFactorySpec extends ObjectBehavior
{
    public function let(FactoryInterface $invoiceShopBillingDataFactory): void
    {
        $this->beConstructedWith(
            Invoice::class,
            $invoiceShopBillingDataFactory,
        );
    }

    function it_implements_invoice_factory_interface(): void
    {
        $this->shouldImplement(InvoiceFactoryInterface::class);
    }

    function it_creates_an_invoice_for_given_data(
        BillingDataInterface $billingData,
        ChannelInterface $channel,
        InvoiceShopBillingDataInterface $invoiceShopBillingData,
        OrderInterface $order,
    ): void {
        $date = new \DateTimeImmutable('2019-03-06');

        $this->createForData(
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            '2019/03/0000001',
            $order,
            $date,
            $billingData,
            'USD',
            'en_US',
            10300,
            new ArrayCollection(),
            new ArrayCollection(),
            $channel,
            InvoiceInterface::PAYMENT_STATE_COMPLETED,
            $invoiceShopBillingData,
        )->shouldReturnAnInstanceOf(InvoiceInterface::class);
    }

    function it_allows_for_nullable_shop_billing_data(
        FactoryInterface $invoiceShopBillingDataFactory,
        OrderInterface $order,
        BillingDataInterface $billingData,
        ChannelInterface $channel,
    ): void {
        $date = new \DateTimeImmutable('2019-03-06');

        $invoiceShopBillingDataFactory->createNew()->willReturn(new InvoiceShopBillingData());

        $this->createForData(
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            '2019/03/0000001',
            $order,
            $date,
            $billingData,
            'USD',
            'en_US',
            10300,
            new ArrayCollection(),
            new ArrayCollection(),
            $channel,
            InvoiceInterface::PAYMENT_STATE_COMPLETED,
            null,
        )->shouldReturnAnInstanceOf(InvoiceInterface::class);
    }
}
