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

namespace spec\Sylius\InvoicingPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Entity\TaxItemInterface;

final class InvoiceSpec extends ObjectBehavior
{
    function let(
        BillingDataInterface $billingData,
        LineItemInterface $lineItem,
        TaxItemInterface $taxItem,
        ChannelInterface $channel,
        InvoiceShopBillingDataInterface $shopBillingData,
        OrderInterface $order,
    ): void {
        $issuedAt = \DateTimeImmutable::createFromFormat('Y-m', '2019-01');

        $this->beConstructedWith(
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            $issuedAt->format('Y/m') . '/000000001',
            $order,
            $issuedAt,
            $billingData,
            'USD',
            'en_US',
            10300,
            new ArrayCollection([$lineItem->getWrappedObject()]),
            new ArrayCollection([$taxItem->getWrappedObject()]),
            $channel,
            InvoiceInterface::PAYMENT_STATE_COMPLETED,
            $shopBillingData,
        );
    }

    function it_implements_invoice_interface(): void
    {
        $this->shouldImplement(InvoiceInterface::class);
    }

    function it_implements_resource_interface(): void
    {
        $this->shouldImplement(ResourceInterface::class);
    }

    function it_has_data(
        BillingDataInterface $billingData,
        LineItemInterface $lineItem,
        TaxItemInterface $taxItem,
        ChannelInterface $channel,
        InvoiceShopBillingDataInterface $shopBillingData,
        OrderInterface $order,
    ): void {
        $this->id()->shouldReturn('7903c83a-4c5e-4bcf-81d8-9dc304c6a353');
        $this->getNumber()->shouldReturn('2019/01/000000001');
        $this->getOrder()->shouldReturn($order);
        $this->getBillingData()->shouldReturn($billingData);
        $this->getCurrencyCode()->shouldReturn('USD');
        $this->getLocaleCode()->shouldReturn('en_US');
        $this->getTotal()->shouldReturn(10300);
        $this->getLineItems()->shouldBeLike(new ArrayCollection([$lineItem->getWrappedObject()]));
        $this->getTaxItems()->shouldBeLike(new ArrayCollection([$taxItem->getWrappedObject()]));
        $this->getChannel()->shouldReturn($channel);
        $this->getShopBillingData()->shouldReturn($shopBillingData);
    }
}
