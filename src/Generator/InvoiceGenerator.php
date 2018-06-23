<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\InvoicingPlugin\Entity\BillingData;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Sylius\InvoicingPlugin\Entity\Invoice;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\LineItem;

final class InvoiceGenerator implements InvoiceGeneratorInterface
{
    /** @var InvoiceIdentifierGenerator */
    private $invoiceIdentifierGenerator;

    public function __construct(InvoiceIdentifierGenerator $invoiceIdentifierGenerator)
    {
        $this->invoiceIdentifierGenerator = $invoiceIdentifierGenerator;
    }

    public function generateForOrder(OrderInterface $order, \DateTimeInterface $date): InvoiceInterface
    {
        $billingAddress = $order->getBillingAddress();

        return new Invoice(
            $this->invoiceIdentifierGenerator->__invoke($order->getNumber()),
            $order->getNumber(),
            $date,
            $this->prepareBillingData($billingAddress),
            $order->getCurrencyCode(),
            $order->getTaxTotal(),
            $order->getTotal(),
            $this->prepareLineItems($order)
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
}
