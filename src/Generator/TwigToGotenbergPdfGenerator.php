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

namespace Sylius\InvoicingPlugin\Generator;

use Sensiolabs\GotenbergBundle\GotenbergInterface;
use Sensiolabs\GotenbergBundle\Processor\ProcessorInterface;

final readonly class TwigToGotenbergPdfGenerator implements TwigToPdfGeneratorInterface
{
    public function __construct(
        private GotenbergInterface $gotenberg
    ) {
    }

    public function generate(string $templateName, array $templateParams): string
    {
        $builder = $this->gotenberg->pdf()
            ->html()
            ->content($templateName, $templateParams)
            ->processor(
                new class implements ProcessorInterface {
                    public function __invoke(?string $fileName): \Generator
                    {
                        $file = '';

                        do {
                            $chunk = yield;
                            $file .= $chunk->getContent();
                        } while (!$chunk->isLast());

                        return $file;
                    }
                }
            )
        ;

        return $builder->generate()->process();
    }
}
