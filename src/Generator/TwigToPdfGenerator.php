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

use Knp\Snappy\GeneratorInterface;
use Twig\Environment;

final class TwigToPdfGenerator implements TwigToPdfGeneratorInterface
{
    public function __construct(
        private readonly Environment $twig,
        private readonly GeneratorInterface $pdfGenerator,
        private readonly PdfOptionsGeneratorInterface $pdfOptionsGenerator,
    ) {
    }

    public function generate(string $templateName, array $templateParams): string
    {
        return $this->pdfGenerator->getOutputFromHtml(
            $this->twig->render($templateName, $templateParams),
            $this->pdfOptionsGenerator->generate(),
        );
    }
}
