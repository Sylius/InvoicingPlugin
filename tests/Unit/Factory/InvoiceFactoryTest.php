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

namespace Tests\Sylius\InvoicingPlugin\Unit\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Sylius\InvoicingPlugin\Entity\Invoice;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingData;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;
use Sylius\InvoicingPlugin\Factory\InvoiceFactory;
use Sylius\InvoicingPlugin\Factory\InvoiceFactoryInterface;

final class InvoiceFactoryTest extends TestCase
{
    private FactoryInterface&MockObject $invoiceShopBillingDataFactory;

    private InvoiceFactory $invoiceFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceShopBillingDataFactory = $this->createMock(FactoryInterface::class);

        $this->invoiceFactory = new InvoiceFactory(
            Invoice::class,
            $this->invoiceShopBillingDataFactory,
        );
    }

    #[Test]
    public function it_implements_invoice_factory_interface(): void
    {
        self::assertInstanceOf(InvoiceFactoryInterface::class, $this->invoiceFactory);
    }

    #[Test]
    public function it_creates_an_invoice_for_given_data(): void
    {
        $billingData = $this->createMock(BillingDataInterface::class);
        $channel = $this->createMock(ChannelInterface::class);
        $invoiceShopBillingData = $this->createMock(InvoiceShopBillingDataInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $date = new \DateTimeImmutable('2019-03-06');

        $result = $this->invoiceFactory->createForData(
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            '2019/03/0000001',
            $order,
            $date,
            $billingData,
            'USD',
            'en_US',
            10300,
            new ArrayCollection(),
            new ArrayCollection(),
            $channel,
            InvoiceInterface::PAYMENT_STATE_COMPLETED,
            $invoiceShopBillingData,
        );

        self::assertInstanceOf(InvoiceInterface::class, $result);
    }

    #[Test]
    public function it_allows_for_nullable_shop_billing_data(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $billingData = $this->createMock(BillingDataInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $date = new \DateTimeImmutable('2019-03-06');

        $this->invoiceShopBillingDataFactory
            ->expects(self::once())
            ->method('createNew')
            ->willReturn(new InvoiceShopBillingData());

        $result = $this->invoiceFactory->createForData(
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            '2019/03/0000001',
            $order,
            $date,
            $billingData,
            'USD',
            'en_US',
            10300,
            new ArrayCollection(),
            new ArrayCollection(),
            $channel,
            InvoiceInterface::PAYMENT_STATE_COMPLETED,
            null,
        );

        self::assertInstanceOf(InvoiceInterface::class, $result);
    }
}
