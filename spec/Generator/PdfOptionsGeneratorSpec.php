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
use Sylius\InvoicingPlugin\Generator\PdfOptionsGeneratorInterface;
use Symfony\Component\Config\FileLocatorInterface;

final class PdfOptionsGeneratorSpec extends ObjectBehavior
{
    function let(FileLocatorInterface $fileLocator): void
    {
        $this->beConstructedWith(
            $fileLocator,
            ['allow' => 'allowed_file_in_knp_snappy_config.png'],
            ['swans.png'],
        );
    }

    function it_is_pdf_options_generator_interface(): void
    {
        $this->shouldImplement(PdfOptionsGeneratorInterface::class);
    }

    function it_generates_pdf_options(FileLocatorInterface $fileLocator): void
    {
        $fileLocator
            ->locate('swans.png')
            ->willReturn('located-path/swans.png');

        $this
            ->generate()
            ->shouldBeLike([
                'allow' => [
                    'allowed_file_in_knp_snappy_config.png',
                    'located-path/swans.png',
                ],
            ])
        ;
    }
}
