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

use Knp\Snappy\GeneratorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Generator\PdfOptionsGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\TwigToPdfGeneratorInterface;
use Twig\Environment;

final class TwigToPdfGeneratorSpec extends ObjectBehavior
{
    function let(
        Environment $twig,
        GeneratorInterface $pdfGenerator,
        PdfOptionsGeneratorInterface $pdfOptionsGenerator,
    ): void {
        $this->beConstructedWith(
            $twig,
            $pdfGenerator,
            $pdfOptionsGenerator,
        );
    }

    function it_is_twig_to_pdf_generator_interface(): void
    {
        $this->shouldImplement(TwigToPdfGeneratorInterface::class);
    }

    function it_generates_pdf_from_twig_template(
        Environment $twig,
        GeneratorInterface $pdfGenerator,
        PdfOptionsGeneratorInterface $pdfOptionsGenerator,
    ): void {
        $twig
            ->render('template.html.twig', ['figcaption' => 'Swans', 'imgPath' => 'located-path/swans.png'])
            ->willReturn('<html>I am a pdf file generated from twig template</html>')
        ;

        $pdfOptionsGenerator
            ->generate()
            ->willReturn(['allow' => ['allowed_file_in_knp_snappy_config.png', 'located-path/swans.png']])
        ;

        $pdfGenerator
            ->getOutputFromHtml(
                '<html>I am a pdf file generated from twig template</html>',
                ['allow' => ['allowed_file_in_knp_snappy_config.png', 'located-path/swans.png']],
            )
            ->willReturn('PDF FILE')
        ;

        $this
            ->generate('template.html.twig', ['figcaption' => 'Swans', 'imgPath' => 'located-path/swans.png'])
            ->shouldBeLike('PDF FILE')
        ;
    }
}
