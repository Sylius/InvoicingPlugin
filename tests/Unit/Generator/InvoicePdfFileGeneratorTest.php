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

namespace Tests\Sylius\InvoicingPlugin\Unit\Generator;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceFileNameGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGenerator;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\TwigToPdfGeneratorInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Symfony\Component\Config\FileLocatorInterface;

final class InvoicePdfFileGeneratorTest extends TestCase
{
    private MockObject&TwigToPdfGeneratorInterface $twigToPdfGenerator;

    private FileLocatorInterface&MockObject $fileLocator;

    private InvoiceFileNameGeneratorInterface&MockObject $invoiceFileNameGenerator;

    private InvoicePdfFileGenerator $generator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->twigToPdfGenerator = $this->createMock(TwigToPdfGeneratorInterface::class);
        $this->fileLocator = $this->createMock(FileLocatorInterface::class);
        $this->invoiceFileNameGenerator = $this->createMock(InvoiceFileNameGeneratorInterface::class);

        $this->generator = new InvoicePdfFileGenerator(
            $this->twigToPdfGenerator,
            $this->fileLocator,
            $this->invoiceFileNameGenerator,
            'invoiceTemplate.html.twig',
            '@SyliusInvoicingPlugin/assets/sylius-logo.png',
        );
    }

    #[Test]
    public function it_implements_invoice_pdf_file_generator_interface(): void
    {
        self::assertInstanceOf(InvoicePdfFileGeneratorInterface::class, $this->generator);
    }

    #[Test]
    public function it_creates_invoice_pdf_with_generated_content_and_filename_basing_on_invoice_number(): void
    {
        $invoice = $this->createMock(InvoiceInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->invoiceFileNameGenerator
            ->expects(self::once())
            ->method('generateForPdf')
            ->with($invoice)
            ->willReturn('2015_05_00004444.pdf');

        $invoice->method('channel')->willReturn($channel);

        $this->fileLocator
            ->expects(self::once())
            ->method('locate')
            ->with('@SyliusInvoicingPlugin/assets/sylius-logo.png')
            ->willReturn('located-path/sylius-logo.png');

        $this->twigToPdfGenerator
            ->expects(self::once())
            ->method('generate')
            ->with('invoiceTemplate.html.twig', ['invoice' => $invoice, 'channel' => $channel, 'invoiceLogoPath' => 'located-path/sylius-logo.png'])
            ->willReturn('PDF FILE');

        $result = $this->generator->generate($invoice);

        $expected = new InvoicePdf('2015_05_00004444.pdf', 'PDF FILE');

        self::assertEquals($expected, $result);
    }
}
