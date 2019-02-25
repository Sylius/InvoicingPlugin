<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Generator;

use Knp\Snappy\GeneratorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceChannelInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Config\FileLocatorInterface;

final class InvoicePdfFileGeneratorSpec extends ObjectBehavior
{
    function let(
        InvoiceRepository $invoiceRepository,
        ChannelRepositoryInterface $channelRepository,
        EngineInterface $twig,
        GeneratorInterface $pdfGenerator,
        FileLocatorInterface $fileLocator
    ): void {
        $this->beConstructedWith(
            $invoiceRepository,
            $channelRepository,
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
        InvoiceRepository $invoiceRepository,
        ChannelRepositoryInterface $channelRepository,
        FileLocatorInterface $fileLocator,
        EngineInterface $twig,
        GeneratorInterface $pdfGenerator,
        InvoiceInterface $invoice,
        ChannelInterface $channel,
        InvoiceChannelInterface $invoiceChannel
    ): void {
        $invoiceRepository->get('000111')->willReturn($invoice);
        $invoice->number()->willReturn('2015/05/00004444');
        $invoice->channel()->willReturn($invoiceChannel);

        $invoiceChannel->getCode()->willReturn('en_US');

        $channelRepository->findOneByCode('en_US')->willReturn($channel);

        $fileLocator->locate('@SyliusInvoicingPlugin/Resources/assets/sylius-logo.png')->willReturn('located-path/sylius-logo.png');

        $twig
            ->render('invoiceTemplate.html.twig', ['invoice' => $invoice, 'channel' => $channel, 'invoiceLogoPath' => 'located-path/sylius-logo.png'])
            ->willReturn('<html>I am an invoice pdf file content</html>')
        ;

        $pdfGenerator->getOutputFromHtml('<html>I am an invoice pdf file content</html>')->willReturn('PDF FILE');

        $this->generate('000111')->shouldBeLike(new InvoicePdf('2015_05_00004444.pdf', 'PDF FILE'));
    }
}
