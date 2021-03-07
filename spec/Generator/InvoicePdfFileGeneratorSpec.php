<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Generator;

use Knp\Snappy\GeneratorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Symfony\Component\Config\FileLocatorInterface;
use Twig\Environment;

final class InvoicePdfFileGeneratorSpec extends ObjectBehavior
{
    function let(
        Environment $twig,
        GeneratorInterface $pdfGenerator,
        FileLocatorInterface $fileLocator
    ): void {
        $this->beConstructedWith(
            $twig,
            $pdfGenerator,
            $fileLocator,
            'invoiceTemplate.html.twig',
            '@SyliusInvoicingPlugin/Resources/assets/sylius-logo.png'
        );
    }

    function it_implements_invoice_pdf_file_generator_interface(): void
    {
        $this->shouldImplement(InvoicePdfFileGeneratorInterface::class);
    }

    function it_creates_invoice_pdf_with_generated_content_and_filename_basing_on_invoice_number(
        FileLocatorInterface $fileLocator,
        Environment $twig,
        GeneratorInterface $pdfGenerator,
        InvoiceInterface $invoice,
        ChannelInterface $channel
    ): void {
        $invoice->number()->willReturn('2015/05/00004444');
        $invoice->channel()->willReturn($channel);

        $fileLocator->locate('@SyliusInvoicingPlugin/Resources/assets/sylius-logo.png')->willReturn('located-path/sylius-logo.png');

        $twig
            ->render('invoiceTemplate.html.twig', ['invoice' => $invoice, 'channel' => $channel, 'invoiceLogoPath' => 'located-path/sylius-logo.png'])
            ->willReturn('<html>I am an invoice pdf file content</html>')
        ;

        $pdfGenerator->getOutputFromHtml('<html>I am an invoice pdf file content</html>')->willReturn('PDF FILE');

        $this->generate($invoice)->shouldBeLike(new InvoicePdf('2015_05_00004444.pdf', 'PDF FILE'));
    }
}
