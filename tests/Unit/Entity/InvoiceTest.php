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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
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
    private const ID = '7903c83a-4c5e-4bcf-81d8-9dc304c6a353';

    private const INVOICE_NUMBER = '2019/01/000000001';

    private const CURRENCY_CODE = 'USD';

    private const LOCALE_CODE = 'en_US';

    private const TOTAL = 10300;

    private BillingDataInterface&MockObject $billingData;

    private LineItemInterface&MockObject $lineItem;

    private MockObject&TaxItemInterface $taxItem;

    private ChannelInterface&MockObject $channel;

    private InvoiceShopBillingDataInterface&MockObject $shopBillingData;

    private MockObject&OrderInterface $order;

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

        $issuedAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2019-01-15 10:30:00');
        self::assertNotFalse($issuedAt);
        $this->issuedAt = $issuedAt;
    }

    #[Test]
    public function it_implements_invoice_interface(): void
    {
        $invoice = $this->createInvoice();

        self::assertInstanceOf(InvoiceInterface::class, $invoice);
    }

    #[Test]
    public function it_implements_resource_interface(): void
    {
        $invoice = $this->createInvoice();

        self::assertInstanceOf(ResourceInterface::class, $invoice);
    }

    #[Test]
    public function it_returns_id(): void
    {
        $invoice = $this->createInvoice();

        self::assertSame(self::ID, $invoice->id());
    }

    #[Test]
    public function it_returns_id_via_get_id_method(): void
    {
        $invoice = $this->createInvoice();

        self::assertSame(self::ID, $invoice->getId());
    }

    #[Test]
    public function it_returns_invoice_number(): void
    {
        $invoice = $this->createInvoice();

        self::assertSame(self::INVOICE_NUMBER, $invoice->number());
    }

    #[Test]
    public function it_returns_order(): void
    {
        $invoice = $this->createInvoice();

        self::assertSame($this->order, $invoice->order());
    }

    #[Test]
    public function it_returns_cloned_issued_at_date(): void
    {
        $invoice = $this->createInvoice();

        $issuedAt = $invoice->issuedAt();

        self::assertEquals($this->issuedAt, $issuedAt);
        self::assertNotSame($this->issuedAt, $issuedAt);
    }

    #[Test]
    public function it_returns_billing_data(): void
    {
        $invoice = $this->createInvoice();

        self::assertSame($this->billingData, $invoice->billingData());
    }

    #[Test]
    public function it_returns_currency_code(): void
    {
        $invoice = $this->createInvoice();

        self::assertSame(self::CURRENCY_CODE, $invoice->currencyCode());
    }

    #[Test]
    public function it_returns_locale_code(): void
    {
        $invoice = $this->createInvoice();

        self::assertSame(self::LOCALE_CODE, $invoice->localeCode());
    }

    #[Test]
    public function it_returns_total(): void
    {
        $invoice = $this->createInvoice();

        self::assertSame(self::TOTAL, $invoice->total());
    }

    #[Test]
    public function it_returns_line_items(): void
    {
        $invoice = $this->createInvoice();

        self::assertEquals(new ArrayCollection([$this->lineItem]), $invoice->lineItems());
    }

    #[Test]
    public function it_returns_tax_items(): void
    {
        $invoice = $this->createInvoice();

        self::assertEquals(new ArrayCollection([$this->taxItem]), $invoice->taxItems());
    }

    #[Test]
    public function it_returns_channel(): void
    {
        $invoice = $this->createInvoice();

        self::assertSame($this->channel, $invoice->channel());
    }

    #[Test]
    public function it_returns_shop_billing_data(): void
    {
        $invoice = $this->createInvoice();

        self::assertSame($this->shopBillingData, $invoice->shopBillingData());
    }

    #[Test]
    public function it_returns_payment_state(): void
    {
        $invoice = $this->createInvoice();

        self::assertSame(InvoiceInterface::PAYMENT_STATE_COMPLETED, $invoice->paymentState());
    }

    #[Test]
    #[DataProvider('subtotalDataProvider')]
    public function it_calculates_subtotal_from_line_items(array $lineItemSubtotals, int $expectedSubtotal): void
    {
        $lineItems = [];
        foreach ($lineItemSubtotals as $subtotal) {
            $lineItem = $this->createMock(LineItemInterface::class);
            $lineItem->method('subtotal')->willReturn($subtotal);
            $lineItem->expects(self::once())->method('setInvoice');
            $lineItems[] = $lineItem;
        }

        $invoice = new Invoice(
            self::ID,
            self::INVOICE_NUMBER,
            $this->order,
            $this->issuedAt,
            $this->billingData,
            self::CURRENCY_CODE,
            self::LOCALE_CODE,
            self::TOTAL,
            new ArrayCollection($lineItems),
            new ArrayCollection([]),
            $this->channel,
            InvoiceInterface::PAYMENT_STATE_COMPLETED,
            $this->shopBillingData,
        );

        self::assertSame($expectedSubtotal, $invoice->subtotal());
    }

    #[Test]
    #[DataProvider('taxesTotalDataProvider')]
    public function it_calculates_taxes_total_from_line_items(array $lineItemTaxes, int $expectedTaxesTotal): void
    {
        $lineItems = [];
        foreach ($lineItemTaxes as $taxTotal) {
            $lineItem = $this->createMock(LineItemInterface::class);
            $lineItem->method('taxTotal')->willReturn($taxTotal);
            $lineItem->expects(self::once())->method('setInvoice');
            $lineItems[] = $lineItem;
        }

        $invoice = new Invoice(
            self::ID,
            self::INVOICE_NUMBER,
            $this->order,
            $this->issuedAt,
            $this->billingData,
            self::CURRENCY_CODE,
            self::LOCALE_CODE,
            self::TOTAL,
            new ArrayCollection($lineItems),
            new ArrayCollection([]),
            $this->channel,
            InvoiceInterface::PAYMENT_STATE_COMPLETED,
            $this->shopBillingData,
        );

        self::assertSame($expectedTaxesTotal, $invoice->taxesTotal());
    }

    #[Test]
    #[DataProvider('pdfSentStatusProvider')]
    public function it_returns_pdf_sent_status(bool $pdfSent): void
    {
        $invoice = $this->createInvoice($pdfSent);

        self::assertSame($pdfSent, $invoice->isPdfSent());
    }

    #[Test]
    public function it_allows_setting_pdf_sent_status(): void
    {
        $invoice = $this->createInvoice(false);

        self::assertFalse($invoice->isPdfSent());

        $invoice->setPdfSent(true);

        self::assertTrue($invoice->isPdfSent());

        $invoice->setPdfSent(false);

        self::assertFalse($invoice->isPdfSent());
    }

    public static function subtotalDataProvider(): array
    {
        return [
            'single line item' => [[1000], 1000],
            'multiple line items' => [[1000, 2000, 1500], 4500],
            'empty line items' => [[], 0],
        ];
    }

    public static function taxesTotalDataProvider(): array
    {
        return [
            'single line item with tax' => [[200], 200],
            'multiple line items with taxes' => [[200, 300, 150], 650],
            'empty line items' => [[], 0],
        ];
    }

    public static function pdfSentStatusProvider(): array
    {
        return [
            'pdf not sent by default' => [false],
            'pdf sent' => [true],
        ];
    }

    private function createInvoice(bool $pdfSent = false): Invoice
    {
        $this->lineItem
            ->expects(self::once())
            ->method('setInvoice')
            ->with($this->isInstanceOf(Invoice::class));

        $this->taxItem
            ->expects(self::once())
            ->method('setInvoice')
            ->with($this->isInstanceOf(Invoice::class));

        return new Invoice(
            self::ID,
            self::INVOICE_NUMBER,
            $this->order,
            $this->issuedAt,
            $this->billingData,
            self::CURRENCY_CODE,
            self::LOCALE_CODE,
            self::TOTAL,
            new ArrayCollection([$this->lineItem]),
            new ArrayCollection([$this->taxItem]),
            $this->channel,
            InvoiceInterface::PAYMENT_STATE_COMPLETED,
            $this->shopBillingData,
            $pdfSent,
        );
    }
}
