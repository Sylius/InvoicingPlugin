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
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\InvoicingPlugin\Converter\BillingDataConverterInterface;
use Sylius\InvoicingPlugin\Converter\InvoiceShopBillingDataConverterInterface;
use Sylius\InvoicingPlugin\Converter\LineItemsConverterInterface;
use Sylius\InvoicingPlugin\Converter\TaxItemsConverterInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Factory\InvoiceFactoryInterface;

final class InvoiceGenerator implements InvoiceGeneratorInterface
{
    /** @var InvoiceIdentifierGenerator */
    private $uuidInvoiceIdentifierGenerator;

    /** @var InvoiceNumberGenerator */
    private $sequentialInvoiceNumberGenerator;

    /** @var InvoiceFactoryInterface */
    private $invoiceFactory;

    /** @var BillingDataConverterInterface */
    private $billingDataConverter;

    /** @var InvoiceShopBillingDataConverterInterface */
    private $invoiceShopBillingDataConverter;

    /** @var LineItemsConverterInterface */
    private $orderItemUnitsToLineItemsConverter;

    /** @var LineItemsConverterInterface */
    private $shippingAdjustmentsToLineItemsConverter;

    /** @var TaxItemsConverterInterface */
    private $taxItemsConverter;

    public function __construct(
        InvoiceIdentifierGenerator $uuidInvoiceIdentifierGenerator,
        InvoiceNumberGenerator $sequentialInvoiceNumberGenerator,
        InvoiceFactoryInterface $invoiceFactory,
        BillingDataConverterInterface $billingDataConverter,
        InvoiceShopBillingDataConverterInterface $invoiceShopBillingDataConverter,
        LineItemsConverterInterface $orderItemUnitsToLineItemsConverter,
        LineItemsConverterInterface $shippingAdjustmentsToLineItemsConverter,
        TaxItemsConverterInterface $taxItemsConverter
    ) {
        $this->uuidInvoiceIdentifierGenerator = $uuidInvoiceIdentifierGenerator;
        $this->sequentialInvoiceNumberGenerator = $sequentialInvoiceNumberGenerator;
        $this->invoiceFactory = $invoiceFactory;
        $this->billingDataConverter = $billingDataConverter;
        $this->invoiceShopBillingDataConverter = $invoiceShopBillingDataConverter;
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

        /** @var bool $isPaid */
        $isPaid = $order->getPaymentState() === PaymentInterface::STATE_COMPLETED;

        return $this->invoiceFactory->createForData(
            $this->uuidInvoiceIdentifierGenerator->generate(),
            $this->sequentialInvoiceNumberGenerator->generate(),
            $order,
            $date,
            $this->billingDataConverter->convert($billingAddress),
            $order->getCurrencyCode(),
            $order->getLocaleCode(),
            $order->getTotal(),
            new ArrayCollection(array_merge(
                $this->orderItemUnitsToLineItemsConverter->convert($order),
                $this->shippingAdjustmentsToLineItemsConverter->convert($order)
            )),
            $this->taxItemsConverter->convert($order),
            $channel,
            $isPaid,
            $this->invoiceShopBillingDataConverter->convert($channel)
        );
    }
}
