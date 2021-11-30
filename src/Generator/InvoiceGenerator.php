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

namespace Sylius\InvoicingPlugin\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\InvoicingPlugin\Converter\LineItemsConverterInterface;
use Sylius\InvoicingPlugin\Converter\TaxItemsConverterInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Factory\BillingDataFactoryInterface;
use Sylius\InvoicingPlugin\Factory\InvoiceFactoryInterface;
use Sylius\InvoicingPlugin\Factory\InvoiceShopBillingDataFactoryInterface;

final class InvoiceGenerator implements InvoiceGeneratorInterface
{
    private InvoiceIdentifierGenerator $uuidInvoiceIdentifierGenerator;

    private InvoiceNumberGenerator $sequentialInvoiceNumberGenerator;

    private InvoiceFactoryInterface $invoiceFactory;

    private BillingDataFactoryInterface $billingDataFactory;

    private InvoiceShopBillingDataFactoryInterface $invoiceShopBillingFactory;

    private LineItemsConverterInterface $orderItemUnitsToLineItemsConverter;

    private LineItemsConverterInterface $shippingAdjustmentsToLineItemsConverter;

    private TaxItemsConverterInterface $taxItemsConverter;

    public function __construct(
        InvoiceIdentifierGenerator $uuidInvoiceIdentifierGenerator,
        InvoiceNumberGenerator $sequentialInvoiceNumberGenerator,
        InvoiceFactoryInterface $invoiceFactory,
        BillingDataFactoryInterface $billingDataFactory,
        InvoiceShopBillingDataFactoryInterface $invoiceShopBillingFactory,
        LineItemsConverterInterface $orderItemUnitsToLineItemsConverter,
        LineItemsConverterInterface $shippingAdjustmentsToLineItemsConverter,
        TaxItemsConverterInterface $taxItemsConverter
    ) {
        $this->uuidInvoiceIdentifierGenerator = $uuidInvoiceIdentifierGenerator;
        $this->sequentialInvoiceNumberGenerator = $sequentialInvoiceNumberGenerator;
        $this->invoiceFactory = $invoiceFactory;
        $this->billingDataFactory = $billingDataFactory;
        $this->invoiceShopBillingFactory = $invoiceShopBillingFactory;
        $this->orderItemUnitsToLineItemsConverter = $orderItemUnitsToLineItemsConverter;
        $this->shippingAdjustmentsToLineItemsConverter = $shippingAdjustmentsToLineItemsConverter;
        $this->taxItemsConverter = $taxItemsConverter;
    }

    public function generateForOrder(OrderInterface $order, \DateTimeInterface $date): InvoiceInterface
    {
        /** @var AddressInterface $billingAddress */
        $billingAddress = $order->getBillingAddress();

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();

        $paymentState = $order->getPaymentState() === OrderPaymentStates::STATE_PAID ?
            InvoiceInterface::PAYMENT_STATE_COMPLETED : InvoiceInterface::PAYMENT_STATE_PENDING;

        return $this->invoiceFactory->createForData(
            $this->uuidInvoiceIdentifierGenerator->generate(),
            $this->sequentialInvoiceNumberGenerator->generate(),
            $order,
            $date,
            $this->billingDataFactory->createFromAddress($billingAddress),
            $order->getCurrencyCode(),
            $order->getLocaleCode(),
            $order->getTotal(),
            new ArrayCollection(array_merge(
                $this->orderItemUnitsToLineItemsConverter->convert($order),
                $this->shippingAdjustmentsToLineItemsConverter->convert($order)
            )),
            $this->taxItemsConverter->convert($order),
            $channel,
            $paymentState,
            $this->invoiceShopBillingFactory->createFromChannel($channel)
        );
    }
}
