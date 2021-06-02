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

namespace spec\Sylius\InvoicingPlugin\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Converter\BillingDataConverterInterface;
use Sylius\InvoicingPlugin\Converter\InvoiceShopBillingDataConverterInterface;
use Sylius\InvoicingPlugin\Converter\LineItemsConverterInterface;
use Sylius\InvoicingPlugin\Converter\TaxItemsConverterInterface;
use Sylius\InvoicingPlugin\Entity\BillingData;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;
use Sylius\InvoicingPlugin\Factory\InvoiceFactoryInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceIdentifierGenerator;
use Sylius\InvoicingPlugin\Generator\InvoiceNumberGenerator;

final class InvoiceGeneratorSpec extends ObjectBehavior
{
    public function let(
        InvoiceIdentifierGenerator $uuidInvoiceIdentifierGenerator,
        InvoiceNumberGenerator $sequentialInvoiceNumberGenerator,
        InvoiceFactoryInterface $invoiceFactory,
        BillingDataConverterInterface $billingDataConverter,
        InvoiceShopBillingDataConverterInterface $invoiceShopBillingDataConverter,
        LineItemsConverterInterface $lineItemConverter,
        TaxItemsConverterInterface $taxItemsConverter
    ): void {
        $this->beConstructedWith(
            $uuidInvoiceIdentifierGenerator,
            $sequentialInvoiceNumberGenerator,
            $invoiceFactory,
            $billingDataConverter,
            $invoiceShopBillingDataConverter,
            $lineItemConverter,
            $taxItemsConverter
        );
    }

    public function it_is_an_invoice_generator(): void
    {
        $this->shouldImplement(InvoiceGeneratorInterface::class);
    }

    public function it_generates_an_invoice_for_a_given_order(
        InvoiceIdentifierGenerator $uuidInvoiceIdentifierGenerator,
        InvoiceNumberGenerator $sequentialInvoiceNumberGenerator,
        InvoiceFactoryInterface $invoiceFactory,
        BillingDataConverterInterface $billingDataConverter,
        InvoiceShopBillingDataConverterInterface $invoiceShopBillingDataConverter,
        LineItemsConverterInterface $lineItemConverter,
        TaxItemsConverterInterface $taxItemsConverter,
        OrderInterface $order,
        AddressInterface $billingAddress,
        ChannelInterface $channel,
        InvoiceShopBillingDataInterface $invoiceShopBillingData,
        BillingData $billingData,
        InvoiceInterface $invoice
    ): void {
        $date = new \DateTimeImmutable('2019-03-06');

        $uuidInvoiceIdentifierGenerator->generate()->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');
        $sequentialInvoiceNumberGenerator->generate()->willReturn($date->format('Y/m') . '/0000001');

        $order->getNumber()->willReturn('007');
        $order->getCurrencyCode()->willReturn('USD');
        $order->getLocaleCode()->willReturn('en_US');
        $order->getTotal()->willReturn(10300);
        $order->getChannel()->willReturn($channel);
        $order->getBillingAddress()->willReturn($billingAddress);

        $mockedLineItems = new ArrayCollection();
        $mockedTaxItems = new ArrayCollection();

        $billingDataConverter->convert($billingAddress)->willReturn($billingData);
        $invoiceShopBillingDataConverter->convert($channel)->willReturn($invoiceShopBillingData);
        $lineItemConverter->convert($order)->willReturn($mockedLineItems);
        $taxItemsConverter->convert($order)->willReturn($mockedTaxItems);

        $invoiceFactory->createForData(
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            '2019/03/0000001',
            '007',
            $date,
            $billingData,
            'USD',
            'en_US',
            10300,
            $mockedLineItems,
            $mockedTaxItems,
            $channel,
            $invoiceShopBillingData
        )->willReturn($invoice);

        $this->generateForOrder($order, $date)->shouldBeLike($invoice);
    }
}
