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

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
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
    private $lineItemsConverter;

    /** @var TaxItemsConverterInterface */
    private $taxItemsConverter;

    public function __construct(
        InvoiceIdentifierGenerator $uuidInvoiceIdentifierGenerator,
        InvoiceNumberGenerator $sequentialInvoiceNumberGenerator,
        InvoiceFactoryInterface $invoiceFactory,
        BillingDataConverterInterface $billingDataConverter,
        InvoiceShopBillingDataConverterInterface $invoiceShopBillingDataConverter,
        LineItemsConverterInterface $lineItemConverter,
        TaxItemsConverterInterface $taxItemsConverter
    ) {
        $this->uuidInvoiceIdentifierGenerator = $uuidInvoiceIdentifierGenerator;
        $this->sequentialInvoiceNumberGenerator = $sequentialInvoiceNumberGenerator;
        $this->invoiceFactory = $invoiceFactory;
        $this->billingDataConverter = $billingDataConverter;
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
            $channel,
            $this->invoiceShopBillingDataConverter->convert($channel)
        );
    }
}
