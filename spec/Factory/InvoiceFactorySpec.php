<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceChannelInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;
use Sylius\InvoicingPlugin\Factory\InvoiceFactoryInterface;

class InvoiceFactorySpec extends ObjectBehavior
{
    function it_implements_invoice_factory_interface(): void
    {
        $this->shouldImplement(InvoiceFactoryInterface::class);
    }

    function it_creates_an_invoice_for_given_data(
        BillingDataInterface $billingData,
        Collection $lineItems,
        Collection $taxItems,
        InvoiceChannelInterface $invoiceChannel,
        InvoiceShopBillingDataInterface $invoiceShopBillingData
    ): void {
        $date = new \DateTimeImmutable('2019-03-06');

        $this->createForData(
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            '2019/03/0000001',
            '007',
            $date,
            $billingData,
            'USD',
            'en_US',
            10300,
            new ArrayCollection(),
            new ArrayCollection(),
            $invoiceChannel,
            $invoiceShopBillingData
        )->shouldReturnAnInstanceOf(InvoiceInterface::class);
    }

    function it_allows_for_nullable_shop_billing_data(
        BillingDataInterface $billingData,
        Collection $lineItems,
        Collection $taxItems,
        InvoiceChannelInterface $invoiceChannel
    ): void {
        $date = new \DateTimeImmutable('2019-03-06');

        $this->createForData(
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            '2019/03/0000001',
            '007',
            $date,
            $billingData,
            'USD',
            'en_US',
            10300,
            new ArrayCollection(),
            new ArrayCollection(),
            $invoiceChannel,
            null
        )->shouldReturnAnInstanceOf(InvoiceInterface::class);
    }
}
