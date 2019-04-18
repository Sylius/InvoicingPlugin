<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\InvoicingPlugin\Entity\BillingData;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Sylius\InvoicingPlugin\Entity\Invoice;
use Sylius\InvoicingPlugin\Entity\InvoiceChannel;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingData;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;
use Sylius\InvoicingPlugin\Entity\LineItem;
use Sylius\InvoicingPlugin\Entity\TaxItem;

final class InvoiceGenerator implements InvoiceGeneratorInterface
{
    /** @var InvoiceIdentifierGenerator */
    private $uuidInvoiceIdentifierGenerator;

    /** @var InvoiceNumberGenerator */
    private $sequentialInvoiceNumberGenerator;

    public function __construct(
        InvoiceIdentifierGenerator $uuidInvoiceIdentifierGenerator,
        InvoiceNumberGenerator $sequentialInvoiceNumberGenerator
    ) {
        $this->uuidInvoiceIdentifierGenerator = $uuidInvoiceIdentifierGenerator;
        $this->sequentialInvoiceNumberGenerator = $sequentialInvoiceNumberGenerator;
    }

    public function generateForOrder(OrderInterface $order, \DateTimeInterface $date): InvoiceInterface
    {
        $billingAddress = $order->getBillingAddress();

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();

        return new Invoice(
            $this->uuidInvoiceIdentifierGenerator->generate(),
            $this->sequentialInvoiceNumberGenerator->generate(),
            $order->getNumber(),
            $date,
            $this->prepareBillingData($billingAddress),
            $order->getCurrencyCode(),
            $order->getLocaleCode(),
            $order->getTotal(),
            $this->prepareLineItems($order),
            $this->prepareTaxItems($order),
            new InvoiceChannel($channel->getCode(), $channel->getName()),
            $this->prepareShopBillingData($channel->getShopBillingData())
        );
    }

    private function prepareBillingData(AddressInterface $billingAddress): BillingDataInterface
    {
        return new BillingData(
            $billingAddress->getFirstName(),
            $billingAddress->getLastName(),
            $billingAddress->getCountryCode(),
            $billingAddress->getStreet(),
            $billingAddress->getCity(),
            $billingAddress->getPostcode(),
            $billingAddress->getProvinceCode(),
            $billingAddress->getProvinceName(),
            $billingAddress->getCompany()
        );
    }

    private function prepareLineItems(OrderInterface $order): Collection
    {
        $orderItems = $order->getItems();
        $shippingAdjustments = $order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        $lineItems = new ArrayCollection();

        /** @var OrderItemInterface $orderItem */
        foreach ($orderItems as $orderItem) {
            $variant = $orderItem->getVariant();

            $lineItems->add(new LineItem(
                $orderItem->getProductName(),
                $orderItem->getQuantity(),
                $orderItem->getUnitPrice(),
                $orderItem->getSubtotal(),
                $orderItem->getTaxTotal(),
                $orderItem->getTotal(),
                $orderItem->getVariantName(),
                $variant !== null ? $variant->getCode() : null
            ));
        }

        /** @var AdjustmentInterface $shippingAdjustment */
        foreach ($shippingAdjustments as $shippingAdjustment) {
            $lineItems->add(new LineItem(
                $shippingAdjustment->getLabel(),
                1,
                $shippingAdjustment->getAmount(),
                $shippingAdjustment->getAmount(),
                0,
                $shippingAdjustment->getAmount()
            ));
        }

        return $lineItems;
    }

    private function prepareTaxItems(OrderInterface $order): Collection
    {
        $temporaryTaxItems = [];
        $taxItems = new ArrayCollection();

        $taxAdjustments = $order->getAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT);
        foreach ($taxAdjustments as $taxAdjustment) {
            if (array_key_exists($taxAdjustment->getLabel(), $temporaryTaxItems)) {
                $temporaryTaxItems[$taxAdjustment->getLabel()] += $taxAdjustment->getAmount();

                continue;
            }

            $temporaryTaxItems[$taxAdjustment->getLabel()] = $taxAdjustment->getAmount();
        }

        foreach ($temporaryTaxItems as $label => $amount) {
            $taxItems->add(new TaxItem($label, $amount));
        }

        return $taxItems;
    }

    private function prepareShopBillingData(?ShopBillingDataInterface $shopBillingData): InvoiceShopBillingDataInterface
    {
        $invoiceShopBillingData = new InvoiceShopBillingData();

        if (null === $shopBillingData) {
            return $invoiceShopBillingData;
        }

        $invoiceShopBillingData->setCompany($shopBillingData->getCompany());
        $invoiceShopBillingData->setTaxId($shopBillingData->getTaxId());
        $invoiceShopBillingData->setCountryCode($shopBillingData->getCountryCode());
        $invoiceShopBillingData->setStreet($shopBillingData->getStreet());
        $invoiceShopBillingData->setCity($shopBillingData->getCity());
        $invoiceShopBillingData->setPostcode($shopBillingData->getPostcode());

        return $invoiceShopBillingData;
    }
}
