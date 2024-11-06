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

namespace spec\Sylius\InvoicingPlugin\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceFileNameGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\TwigToPdfGeneratorInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Symfony\Component\Config\FileLocatorInterface;

final class InvoicePdfFileGeneratorSpec extends ObjectBehavior
{
    function let(
        TwigToPdfGeneratorInterface $twigToPdfGenerator,
        FileLocatorInterface $fileLocator,
        InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator,
    ): void {
        $this->beConstructedWith(
            $twigToPdfGenerator,
            $fileLocator,
            $invoiceFileNameGenerator,
            'invoiceTemplate.html.twig',
            '@SyliusInvoicingPlugin/Resources/assets/sylius-logo.png',
        );
    }

    function it_implements_invoice_pdf_file_generator_interface(): void
    {
        $this->shouldImplement(InvoicePdfFileGeneratorInterface::class);
    }

    function it_creates_invoice_pdf_with_generated_content_and_filename_basing_on_invoice_number(
        TwigToPdfGeneratorInterface $twigToPdfGenerator,
        FileLocatorInterface $fileLocator,
        InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator,
        InvoiceInterface $invoice,
        ChannelInterface $channel,
    ): void {
        $invoiceFileNameGenerator->generateForPdf($invoice)->willReturn('2015_05_00004444.pdf');
        $invoice->channel()->willReturn($channel);

        $fileLocator->locate('@SyliusInvoicingPlugin/Resources/assets/sylius-logo.png')->willReturn('located-path/sylius-logo.png');

        $twigToPdfGenerator
            ->generate('invoiceTemplate.html.twig', ['invoice' => $invoice, 'channel' => $channel, 'invoiceLogoPath' => 'located-path/sylius-logo.png'])
            ->willReturn('PDF FILE')
        ;

        $this
            ->generate($invoice)
            ->shouldBeLike(new InvoicePdf('2015_05_00004444.pdf', 'PDF FILE'))
        ;
    }
}
