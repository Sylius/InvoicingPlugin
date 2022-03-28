<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Generator;

use Knp\Snappy\GeneratorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Generator\TwigToPdfGeneratorInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Twig\Environment;

final class TwigToPdfGeneratorSpec extends ObjectBehavior
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
            ['swans.png']
        );
    }

    function it_is_twig_to_pdf_generator_interface(): void
    {
        $this->shouldImplement(TwigToPdfGeneratorInterface::class);
    }

    function it_generates_pdf_from_twig_template(
        Environment $twig,
        GeneratorInterface $pdfGenerator,
        FileLocatorInterface $fileLocator
    ): void {
        $twig
            ->render('template.html.twig', ['figcaption' => 'Swans', 'imgPath' => 'located-path/swans.png'])
            ->willReturn('<html>I am a pdf file generated from twig template</html>')
        ;

        $pdfGenerator
            ->getOutputFromHtml(
                '<html>I am a pdf file generated from twig template</html>',
                ['allow' => ['located-path/swans.png']]
            )
            ->willReturn('PDF FILE')
        ;

        $fileLocator
            ->locate('swans.png')
            ->willReturn('located-path/swans.png')
        ;

        $this
            ->generate('template.html.twig', ['figcaption' => 'Swans', 'imgPath' => 'located-path/swans.png'])
            ->shouldBeLike('PDF FILE')
        ;
    }
}
