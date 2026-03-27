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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Exception\InvoiceFileGenerationFailedException;
use Sylius\InvoicingPlugin\Generator\InvoiceFileNameGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Manager\InvoiceFileManagerInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Sylius\InvoicingPlugin\Provider\InvoiceFileProvider;
use Sylius\InvoicingPlugin\Provider\InvoiceFileProviderInterface;

final class InvoiceFileProviderTest extends TestCase
{
    private const INVOICE_FILENAME = 'invoice_2024_11_0001.pdf';

    private const INVOICES_DIRECTORY = '/path/to/invoices';

    private const PDF_CONTENT = 'PDF_CONTENT';

    private InvoiceFileNameGeneratorInterface&MockObject $invoiceFileNameGenerator;

    private FilesystemInterface&MockObject $filesystem;

    private InvoicePdfFileGeneratorInterface&MockObject $invoicePdfFileGenerator;

    private InvoiceFileManagerInterface&MockObject $invoiceFileManager;

    private InvoiceInterface&MockObject $invoice;

    private InvoiceFileProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceFileNameGenerator = $this->createMock(InvoiceFileNameGeneratorInterface::class);
        $this->filesystem = $this->createMock(FilesystemInterface::class);
        $this->invoicePdfFileGenerator = $this->createMock(InvoicePdfFileGeneratorInterface::class);
        $this->invoiceFileManager = $this->createMock(InvoiceFileManagerInterface::class);
        $this->invoice = $this->createMock(InvoiceInterface::class);

        $this->provider = new InvoiceFileProvider(
            $this->invoiceFileNameGenerator,
            $this->filesystem,
            $this->invoicePdfFileGenerator,
            $this->invoiceFileManager,
            self::INVOICES_DIRECTORY,
        );
    }

    #[Test]
    public function it_implements_invoice_file_provider_interface(): void
    {
        self::assertInstanceOf(InvoiceFileProviderInterface::class, $this->provider);
    }

    #[Test]
    #[DataProvider('invoiceFileDataProvider')]
    public function it_provides_existing_invoice_file_from_filesystem(string $fileName, string $content): void
    {
        $invoiceFile = $this->createMock(File::class);

        $this->invoiceFileNameGenerator
            ->expects(self::once())
            ->method('generateForPdf')
            ->with($this->invoice)
            ->willReturn($fileName);

        $this->filesystem
            ->expects(self::once())
            ->method('get')
            ->with($fileName)
            ->willReturn($invoiceFile);

        $invoiceFile
            ->expects(self::once())
            ->method('getContent')
            ->willReturn($content);

        $this->invoicePdfFileGenerator
            ->expects(self::never())
            ->method('generate');

        $this->invoiceFileManager
            ->expects(self::never())
            ->method('save');

        $result = $this->provider->provide($this->invoice);

        $expected = $this->createExpectedInvoicePdf($fileName, $content);

        self::assertEquals($expected, $result);
    }

    #[Test]
    #[DataProvider('invoiceFileDataProvider')]
    public function it_generates_and_saves_invoice_when_file_not_found(string $fileName, string $content): void
    {
        $this->invoiceFileNameGenerator
            ->expects(self::once())
            ->method('generateForPdf')
            ->with($this->invoice)
            ->willReturn($fileName);

        $this->filesystem
            ->expects(self::once())
            ->method('get')
            ->with($fileName)
            ->willThrowException(new FileNotFound($fileName));

        $generatedInvoicePdf = new InvoicePdf($fileName, $content);

        $this->invoicePdfFileGenerator
            ->expects(self::once())
            ->method('generate')
            ->with($this->invoice)
            ->willReturn($generatedInvoicePdf);

        $this->invoiceFileManager
            ->expects(self::once())
            ->method('save')
            ->with($generatedInvoicePdf);

        $result = $this->provider->provide($this->invoice);

        $expected = $this->createExpectedInvoicePdf($fileName, $content);

        self::assertEquals($expected, $result);
    }

    #[Test]
    #[DataProvider('generationExceptionProvider')]
    public function it_throws_invoice_file_generation_failed_exception_when_generation_fails(\Throwable $exception): void
    {
        $this->expectException(InvoiceFileGenerationFailedException::class);

        $this->expectInvoiceFileNameGeneration();

        $this->filesystem
            ->expects(self::once())
            ->method('get')
            ->with(self::INVOICE_FILENAME)
            ->willThrowException(new FileNotFound(self::INVOICE_FILENAME));

        $this->invoicePdfFileGenerator
            ->expects(self::once())
            ->method('generate')
            ->with($this->invoice)
            ->willThrowException($exception);

        $this->invoiceFileManager
            ->expects(self::never())
            ->method('save');

        $this->provider->provide($this->invoice);
    }

    #[Test]
    public function it_throws_invoice_file_generation_failed_exception_when_save_fails(): void
    {
        $this->expectException(InvoiceFileGenerationFailedException::class);

        $this->expectInvoiceFileNameGeneration();

        $this->filesystem
            ->expects(self::once())
            ->method('get')
            ->with(self::INVOICE_FILENAME)
            ->willThrowException(new FileNotFound(self::INVOICE_FILENAME));

        $generatedInvoicePdf = new InvoicePdf(self::INVOICE_FILENAME, self::PDF_CONTENT);

        $this->invoicePdfFileGenerator
            ->expects(self::once())
            ->method('generate')
            ->with($this->invoice)
            ->willReturn($generatedInvoicePdf);

        $this->invoiceFileManager
            ->expects(self::once())
            ->method('save')
            ->with($generatedInvoicePdf)
            ->willThrowException(new \RuntimeException('Failed to save file'));

        $this->provider->provide($this->invoice);
    }

    public static function invoiceFileDataProvider(): array
    {
        return [
            'standard filename and content' => ['invoice_2024_11_0001.pdf', 'PDF_CONTENT'],
            'custom filename' => ['custom_invoice.pdf', 'CUSTOM_CONTENT'],
            'filename with special chars' => ['invoice-special_#123.pdf', 'SPECIAL_CONTENT'],
        ];
    }

    public static function generationExceptionProvider(): array
    {
        return [
            'RuntimeException' => [new \RuntimeException('Failed to generate PDF')],
            'InvalidArgumentException' => [new \InvalidArgumentException('Invalid template')],
            'LogicException' => [new \LogicException('Logic error in generation')],
        ];
    }

    private function expectInvoiceFileNameGeneration(): void
    {
        $this->invoiceFileNameGenerator
            ->expects(self::once())
            ->method('generateForPdf')
            ->with($this->invoice)
            ->willReturn(self::INVOICE_FILENAME);
    }

    private function createExpectedInvoicePdf(string $fileName = self::INVOICE_FILENAME, string $content = self::PDF_CONTENT): InvoicePdf
    {
        $invoicePdf = new InvoicePdf($fileName, $content);
        $invoicePdf->setFullPath(self::INVOICES_DIRECTORY . '/' . $fileName);

        return $invoicePdf;
    }
}
