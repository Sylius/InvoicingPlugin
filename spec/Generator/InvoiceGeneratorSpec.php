<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShopBillingData;
use Sylius\InvoicingPlugin\Entity\Invoice;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceIdentifierGenerator;
use Sylius\InvoicingPlugin\Generator\InvoiceNumberGenerator;

final class InvoiceGeneratorSpec extends ObjectBehavior
{
    function let(
        InvoiceIdentifierGenerator $uuidInvoiceIdentifierGenerator,
        InvoiceNumberGenerator $sequentialInvoiceNumberGenerator
    ): void {
        $this->beConstructedWith($uuidInvoiceIdentifierGenerator, $sequentialInvoiceNumberGenerator);
    }

    function it_is_an_invoice_generator(): void
    {
        $this->shouldImplement(InvoiceGeneratorInterface::class);
    }

    function it_generates_an_invoice_for_a_given_order(
        InvoiceIdentifierGenerator $uuidInvoiceIdentifierGenerator,
        InvoiceNumberGenerator $sequentialInvoiceNumberGenerator,
        OrderInterface $order,
        AddressInterface $billingAddress,
        AdjustmentInterface $shippingAdjustment,
        ProductVariantInterface $variant,
        OrderItemInterface $orderItem,
        AdjustmentInterface $taxAdjustment,
        ChannelInterface $channel,
        ShopBillingData $shopBillingData
    ): void {
        $date = new \DateTimeImmutable('now');

        $uuidInvoiceIdentifierGenerator->generate()->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');
        $sequentialInvoiceNumberGenerator->generate()->willReturn($date->format('Y/m') . '0000001');

        $order->getNumber()->willReturn('007');
        $order->getCurrencyCode()->willReturn('USD');
        $order->getLocaleCode()->willReturn('en_US');
        $order->getTotal()->willReturn(10300);
        $order->getBillingAddress()->willReturn($billingAddress);
        $order->getItems()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));
        $order
            ->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$shippingAdjustment->getWrappedObject()]))
        ;
        $order->getChannel()->willReturn($channel);

        $channel->getCode()->willReturn('WEB-US');
        $channel->getName()->willReturn('United States');

        $channel->getShopBillingData()->willReturn($shopBillingData);

        $shopBillingData->getPostcode()->willReturn('11-111');
        $shopBillingData->getCompany()->willReturn('sample_company');
        $shopBillingData->getCountryCode()->willReturn('US');
        $shopBillingData->getStreet()->willReturn('sample_street');
        $shopBillingData->getTaxId()->willReturn('110011001');
        $shopBillingData->getCity()->willReturn('sample_city');

        $billingAddress->getFirstName()->willReturn('John');
        $billingAddress->getLastName()->willReturn('Doe');
        $billingAddress->getCountryCode()->willReturn('US');
        $billingAddress->getStreet()->willReturn('Foo Street');
        $billingAddress->getCity()->willReturn('New York');
        $billingAddress->getPostcode()->willReturn('21354');
        $billingAddress->getProvinceCode()->willReturn(null);
        $billingAddress->getProvinceName()->willReturn(null);
        $billingAddress->getCompany()->willReturn(null);

        $orderItem->getProductName()->willReturn('Mjolnir');
        $orderItem->getQuantity()->willReturn(2);
        $orderItem->getUnitPrice()->willReturn(5000);
        $orderItem->getSubtotal()->willReturn(10000);
        $orderItem->getTaxTotal()->willReturn(500);
        $orderItem->getTotal()->willReturn(10300);
        $orderItem->getVariantName()->willReturn('Blue');
        $orderItem->getVariant()->willReturn($variant);

        $variant->getCode()->willReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');

        $shippingAdjustment->getLabel()->willReturn('UPS');
        $shippingAdjustment->getAmount()->willReturn(800);

        $order
            ->getAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment->getWrappedObject()]))
        ;

        $taxAdjustment->getLabel()->willReturn('VAT (10%)');
        $taxAdjustment->getAmount()->willReturn(500);

        $this->generateForOrder($order, $date)->shouldReturnAnInstanceOf(Invoice::class);
    }
}
