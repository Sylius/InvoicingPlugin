<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Generator;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Converter\BillingDataConverterInterface;
use Sylius\InvoicingPlugin\Converter\InvoiceChannelConverterInterface;
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

    /** @var InvoiceChannelConverterInterface */
    private $invoiceChannelConverter;

    /** @var InvoiceShopBillingDataConverterInterface */
    private $invoiceShopBillingDataConverter;

    /** @var LineItemsConverterInterface */
    private $lineItemsConverter;

    /** @var TaxItemsConverterInterface */
    private $taxItemsConverter;

    public function __construct(
        InvoiceIdentifierGenerator $uuidInvoiceIdentifierGenerator,
        InvoiceNumberGenerator $sequentialInvoiceNumberGenerator,
        InvoiceFactoryInterface $invoiceFactory,
        BillingDataConverterInterface $billingDataConverter,
        InvoiceChannelConverterInterface $invoiceChannelConverter,
        InvoiceShopBillingDataConverterInterface $invoiceShopBillingDataConverter,
        LineItemsConverterInterface $lineItemConverter,
        TaxItemsConverterInterface $taxItemsConverter
    ) {
        $this->uuidInvoiceIdentifierGenerator = $uuidInvoiceIdentifierGenerator;
        $this->sequentialInvoiceNumberGenerator = $sequentialInvoiceNumberGenerator;
        $this->invoiceFactory = $invoiceFactory;
        $this->billingDataConverter = $billingDataConverter;
        $this->invoiceChannelConverter = $invoiceChannelConverter;
        $this->invoiceShopBillingDataConverter = $invoiceShopBillingDataConverter;
        $this->lineItemsConverter = $lineItemConverter;
        $this->taxItemsConverter = $taxItemsConverter;
    }

    public function generateForOrder(OrderInterface $order, \DateTimeInterface $date): InvoiceInterface
    {
        /** @var AddressInterface $billingAddress */
        $billingAddress = $order->getBillingAddress();

        /** @var ChannelInterface $channel */
        $channel = $order->getChannel();

        return $this->invoiceFactory->createForData(
            $this->uuidInvoiceIdentifierGenerator->generate(),
            $this->sequentialInvoiceNumberGenerator->generate(),
            $order->getNumber(),
            $date,
            $this->billingDataConverter->convert($billingAddress),
            $order->getCurrencyCode(),
            $order->getLocaleCode(),
            $order->getTotal(),
            $this->lineItemsConverter->convert($order),
            $this->taxItemsConverter->convert($order),
            $this->invoiceChannelConverter->convert($channel),
            $this->invoiceShopBillingDataConverter->convert($channel)
        );
    }
}
