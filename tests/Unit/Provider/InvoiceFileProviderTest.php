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

namespace Tests\Sylius\InvoicingPlugin\Unit\Provider;

use Gaufrette\Exception\FileNotFound;
use Gaufrette\File;
use Gaufrette\FilesystemInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceFileNameGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Manager\InvoiceFileManagerInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Sylius\InvoicingPlugin\Provider\InvoiceFileProvider;
use Sylius\InvoicingPlugin\Provider\InvoiceFileProviderInterface;

final class InvoiceFileProviderTest extends TestCase
{
    private InvoiceFileNameGeneratorInterface&MockObject $invoiceFileNameGenerator;

    private FilesystemInterface&MockObject $filesystem;

    private InvoicePdfFileGeneratorInterface&MockObject $invoicePdfFileGenerator;

    private InvoiceFileManagerInterface&MockObject $invoiceFileManager;

    private InvoiceFileProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceFileNameGenerator = $this->createMock(InvoiceFileNameGeneratorInterface::class);
        $this->filesystem = $this->createMock(FilesystemInterface::class);
        $this->invoicePdfFileGenerator = $this->createMock(InvoicePdfFileGeneratorInterface::class);
        $this->invoiceFileManager = $this->createMock(InvoiceFileManagerInterface::class);

        $this->provider = new InvoiceFileProvider(
            $this->invoiceFileNameGenerator,
            $this->filesystem,
            $this->invoicePdfFileGenerator,
            $this->invoiceFileManager,
            '/path/to/invoices',
        );
    }

    #[Test]
    public function it_implements_invoice_file_provider_interface(): void
    {
        self::assertInstanceOf(InvoiceFileProviderInterface::class, $this->provider);
    }

    #[Test]
    public function it_provides_invoice_file_for_invoice(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
        $invoiceFile = $this->createMock(File::class);

        $this->invoiceFileNameGenerator
            ->expects(self::once())
            ->method('generateForPdf')
            ->with($invoice)
            ->willReturn('invoice.pdf');

        $this->filesystem
            ->expects(self::once())
            ->method('get')
            ->with('invoice.pdf')
            ->willReturn($invoiceFile);

        $invoiceFile
            ->expects(self::once())
            ->method('getContent')
            ->willReturn('CONTENT');

        $result = $this->provider->provide($invoice);

        $expected = new InvoicePdf('invoice.pdf', 'CONTENT');
        $expected->setFullPath('/path/to/invoices/invoice.pdf');

        self::assertEquals($expected, $result);
    }

    #[Test]
    public function it_generates_invoice_if_it_does_not_exist_and_provides_it(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);

        $this->invoiceFileNameGenerator
            ->expects(self::once())
            ->method('generateForPdf')
            ->with($invoice)
            ->willReturn('invoice.pdf');

        $this->filesystem
            ->expects(self::once())
            ->method('get')
            ->with('invoice.pdf')
            ->willThrowException(new FileNotFound('invoice.pdf'));

        $invoicePdf = new InvoicePdf('invoice.pdf', 'CONTENT');
        $invoicePdf->setFullPath('/path/to/invoices/invoice.pdf');

        $this->invoicePdfFileGenerator
            ->expects(self::once())
            ->method('generate')
            ->with($invoice)
            ->willReturn($invoicePdf);

        $this->invoiceFileManager
            ->expects(self::once())
            ->method('save')
            ->with($invoicePdf);

        $result = $this->provider->provide($invoice);

        $expected = new InvoicePdf('invoice.pdf', 'CONTENT');
        $expected->setFullPath('/path/to/invoices/invoice.pdf');

        self::assertEquals($expected, $result);
    }
}
