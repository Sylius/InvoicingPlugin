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
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGenerator;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\TwigToPdfGeneratorInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Sylius\PdfGenerationBundle\Core\Renderer\TwigToPdfRendererInterface;
use Symfony\Component\Config\FileLocatorInterface;

final class InvoicePdfFileGeneratorTest extends TestCase
{
    private FileLocatorInterface&MockObject $fileLocator;

    #[Test]
    public function it_implements_invoice_pdf_file_generator_interface(): void
    {
        $generator = new InvoicePdfFileGenerator(
            $this->createMock(TwigToPdfGeneratorInterface::class),
            $this->createMock(FileLocatorInterface::class),
            'invoiceTemplate.html.twig',
            '@SyliusInvoicingPlugin/assets/sylius-logo.png',
        );

        self::assertInstanceOf(InvoicePdfFileGeneratorInterface::class, $generator);
    }

    #[Test]
    public function it_creates_invoice_pdf_using_legacy_twig_to_pdf_generator(): void
    {
        $twigToPdfGenerator = $this->createMock(TwigToPdfGeneratorInterface::class);
        $this->setUpCommonDependencies();

        $generator = new InvoicePdfFileGenerator(
            $twigToPdfGenerator,
            $this->fileLocator,
            'invoiceTemplate.html.twig',
            '@SyliusInvoicingPlugin/assets/sylius-logo.png',
        );

        $invoice = $this->createMock(InvoiceInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $logoPath = __DIR__ . '/../../../assets/sylius-logo.png';
        $logoDataUri = 'data:image/png;base64,' . base64_encode((string) file_get_contents($logoPath));

        $invoice
            ->expects(self::once())
            ->method('path')
            ->willReturn('invoice.pdf');

        $invoice->method('channel')->willReturn($channel);

        $this->fileLocator
            ->expects(self::once())
            ->method('locate')
            ->with('@SyliusInvoicingPlugin/assets/sylius-logo.png')
            ->willReturn($logoPath);

        $twigToPdfGenerator
            ->expects(self::once())
            ->method('generate')
            ->with('invoiceTemplate.html.twig', [
                'invoice' => $invoice,
                'channel' => $channel,
                'invoiceLogoPath' => $logoPath,
                'invoiceLogo' => $logoDataUri,
            ])
            ->willReturn('PDF FILE');

        $result = $generator->generate($invoice);

        $expected = new InvoicePdf('invoice.pdf', 'PDF FILE');

        self::assertEquals($expected, $result);
    }

    #[Test]
    public function it_creates_invoice_pdf_using_pdf_bundle_twig_to_pdf_renderer(): void
    {
        $twigToPdfRenderer = $this->createMock(TwigToPdfRendererInterface::class);
        $this->setUpCommonDependencies();

        $generator = new InvoicePdfFileGenerator(
            $twigToPdfRenderer,
            $this->fileLocator,
            'invoiceTemplate.html.twig',
            '@SyliusInvoicingPlugin/assets/sylius-logo.png',
        );

        $invoice = $this->createMock(InvoiceInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $logoPath = __DIR__ . '/../../../assets/sylius-logo.png';
        $logoDataUri = 'data:image/png;base64,' . base64_encode((string) file_get_contents($logoPath));

        $invoice
            ->expects(self::once())
            ->method('path')
            ->willReturn('2015_05_00004444.pdf');

        $invoice->method('channel')->willReturn($channel);

        $this->fileLocator
            ->expects(self::once())
            ->method('locate')
            ->with('@SyliusInvoicingPlugin/assets/sylius-logo.png')
            ->willReturn($logoPath);

        $twigToPdfRenderer
            ->expects(self::once())
            ->method('render')
            ->with(
                'invoiceTemplate.html.twig',
                [
                    'invoice' => $invoice,
                    'channel' => $channel,
                    'invoiceLogoPath' => $logoPath,
                    'invoiceLogo' => $logoDataUri,
                ],
                'sylius_invoicing',
            )
            ->willReturn('PDF FILE');

        $result = $generator->generate($invoice);

        $expected = new InvoicePdf('2015_05_00004444.pdf', 'PDF FILE');

        self::assertEquals($expected, $result);
    }

    private function setUpCommonDependencies(): void
    {
        $this->fileLocator = $this->createMock(FileLocatorInterface::class);
    }
}
