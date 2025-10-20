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

namespace Tests\Sylius\InvoicingPlugin\Unit\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Sylius\InvoicingPlugin\Entity\Invoice;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Entity\TaxItemInterface;

final class InvoiceTest extends TestCase
{
    private BillingDataInterface $billingData;

    private LineItemInterface $lineItem;

    private TaxItemInterface $taxItem;

    private ChannelInterface $channel;

    private InvoiceShopBillingDataInterface $shopBillingData;

    private OrderInterface $order;

    private Invoice $invoice;

    private \DateTimeImmutable $issuedAt;

    protected function setUp(): void
    {
        parent::setUp();
        $this->billingData = $this->createMock(BillingDataInterface::class);
        $this->lineItem = $this->createMock(LineItemInterface::class);
        $this->taxItem = $this->createMock(TaxItemInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->shopBillingData = $this->createMock(InvoiceShopBillingDataInterface::class);
        $this->order = $this->createMock(OrderInterface::class);

        $this->issuedAt = \DateTimeImmutable::createFromFormat('Y-m', '2019-01');

        $this->lineItem->expects(self::once())
            ->method('setInvoice')
            ->with($this->isInstanceOf(Invoice::class));

        $this->taxItem->expects(self::once())
            ->method('setInvoice')
            ->with($this->isInstanceOf(Invoice::class));

        $this->invoice = new Invoice(
            '7903c83a-4c5e-4bcf-81d8-9dc304c6a353',
            $this->issuedAt->format('Y/m') . '/000000001',
            $this->order,
            $this->issuedAt,
            $this->billingData,
            'USD',
            'en_US',
            10300,
            new ArrayCollection([$this->lineItem]),
            new ArrayCollection([$this->taxItem]),
            $this->channel,
            InvoiceInterface::PAYMENT_STATE_COMPLETED,
            $this->shopBillingData,
        );
    }

    #[Test]
    public function it_implements_invoice_interface(): void
    {
        self::assertInstanceOf(InvoiceInterface::class, $this->invoice);
    }

    #[Test]
    public function it_implements_resource_interface(): void
    {
        self::assertInstanceOf(ResourceInterface::class, $this->invoice);
    }

    #[Test]
    public function it_has_data(): void
    {
        self::assertSame('7903c83a-4c5e-4bcf-81d8-9dc304c6a353', $this->invoice->id());
        self::assertSame('2019/01/000000001', $this->invoice->getNumber());
        self::assertSame($this->order, $this->invoice->getOrder());
        self::assertSame($this->billingData, $this->invoice->getBillingData());
        self::assertSame('USD', $this->invoice->getCurrencyCode());
        self::assertSame('en_US', $this->invoice->getLocaleCode());
        self::assertSame(10300, $this->invoice->getTotal());
        self::assertEquals(new ArrayCollection([$this->lineItem]), $this->invoice->getLineItems());
        $this->assertEquals(new ArrayCollection([$this->taxItem]), $this->invoice->getTaxItems());
        $this->assertSame($this->channel, $this->invoice->getChannel());
        $this->assertSame($this->shopBillingData, $this->invoice->getShopBillingData());
    }
}
