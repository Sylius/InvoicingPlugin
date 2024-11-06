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

namespace spec\Sylius\InvoicingPlugin\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\InvoicingPlugin\Converter\LineItemsConverterInterface;
use Sylius\InvoicingPlugin\Converter\TaxItemsConverterInterface;
use Sylius\InvoicingPlugin\Entity\BillingData;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Entity\TaxItemInterface;
use Sylius\InvoicingPlugin\Factory\BillingDataFactoryInterface;
use Sylius\InvoicingPlugin\Factory\InvoiceFactoryInterface;
use Sylius\InvoicingPlugin\Factory\InvoiceShopBillingDataFactoryInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceIdentifierGenerator;
use Sylius\InvoicingPlugin\Generator\InvoiceNumberGenerator;

final class InvoiceGeneratorSpec extends ObjectBehavior
{
    function let(
        InvoiceIdentifierGenerator $uuidInvoiceIdentifierGenerator,
        InvoiceNumberGenerator $sequentialInvoiceNumberGenerator,
        InvoiceFactoryInterface $invoiceFactory,
        BillingDataFactoryInterface $billingDataFactory,
        InvoiceShopBillingDataFactoryInterface $invoiceShopBillingDataFactory,
        LineItemsConverterInterface $orderItemUnitsToLineItemsConverter,
        LineItemsConverterInterface $shippingAdjustmentsToLineItemsConverter,
        TaxItemsConverterInterface $taxItemsConverter,
    ): void {
        $this->beConstructedWith(
            $uuidInvoiceIdentifierGenerator,
            $sequentialInvoiceNumberGenerator,
            $invoiceFactory,
            $billingDataFactory,
            $invoiceShopBillingDataFactory,
            $orderItemUnitsToLineItemsConverter,
            $shippingAdjustmentsToLineItemsConverter,
            $taxItemsConverter,
        );
    }

    function it_is_an_invoice_generator(): void
    {
        $this->shouldImplement(InvoiceGeneratorInterface::class);
    }

    function it_generates_an_invoice_for_a_given_order(
        InvoiceIdentifierGenerator $uuidInvoiceIdentifierGenerator,
        InvoiceNumberGenerator $sequentialInvoiceNumberGenerator,
        InvoiceFactoryInterface $invoiceFactory,
        BillingDataFactoryInterface $billingDataFactory,
        InvoiceShopBillingDataFactoryInterface $invoiceShopBillingDataFactory,
        LineItemsConverterInterface $orderItemUnitsToLineItemsConverter,
        LineItemsConverterInterface $shippingAdjustmentsToLineItemsConverter,
        TaxItemsConverterInterface $taxItemsConverter,
        OrderInterface $order,
        AddressInterface $billingAddress,
        ChannelInterface $channel,
        InvoiceShopBillingDataInterface $invoiceShopBillingData,
        BillingData $billingData,
        InvoiceInterface $invoice,
        LineItemInterface $unitLineItem,
        LineItemInterface $shippingLineItem,
        TaxItemInterface $taxItem,
    ): void {
        $date = new \DateTimeImmutable('2019-03-06');

        $uuidInvoiceIdentifierGenerator->generate()->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');
        $sequentialInvoiceNumberGenerator->generate()->willReturn($date->format('Y/m') . '/0000001');

        $order->getCurrencyCode()->willReturn('USD');
        $order->getLocaleCode()->willReturn('en_US');
        $order->getTotal()->willReturn(10300);
        $order->getChannel()->willReturn($channel);
        $order->getPaymentState()->willReturn(OrderPaymentStates::STATE_PAID);
        $order->getBillingAddress()->willReturn($billingAddress);

        $billingDataFactory->createFromAddress($billingAddress)->willReturn($billingData);
        $invoiceShopBillingDataFactory->createFromChannel($channel)->willReturn($invoiceShopBillingData);

        $orderItemUnitsToLineItemsConverter->convert($order)->willReturn([$unitLineItem->getWrappedObject()]);
        $shippingAdjustmentsToLineItemsConverter->convert($order)->willReturn([$shippingLineItem->getWrappedObject()]);
        $taxItemsConverter->convert($order)->willReturn(new ArrayCollection([$taxItem->getWrappedObject()]));

        $invoiceFactory->createForData(
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            '2019/03/0000001',
            $order,
            $date,
            $billingData,
            'USD',
            'en_US',
            10300,
            new ArrayCollection([$unitLineItem->getWrappedObject(), $shippingLineItem->getWrappedObject()]),
            new ArrayCollection([$taxItem->getWrappedObject()]),
            $channel,
            InvoiceInterface::PAYMENT_STATE_COMPLETED,
            $invoiceShopBillingData,
        )->willReturn($invoice);

        $this->generateForOrder($order, $date)->shouldBeLike($invoice);
    }
}
