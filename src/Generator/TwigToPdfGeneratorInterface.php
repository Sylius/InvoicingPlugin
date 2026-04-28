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

/**
 * @deprecated since sylius/invoicing-plugin 2.2, use Sylius\PdfGenerationBundle\Core\Renderer\TwigToPdfRendererInterface from sylius/pdf-generation-bundle instead.
 */
interface TwigToPdfGeneratorInterface
{
    public function generate(string $templateName, array $templateParams): string;
}
