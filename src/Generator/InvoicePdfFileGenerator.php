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

use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Sylius\PdfGenerationBundle\Core\Renderer\TwigToPdfRendererInterface;
use Symfony\Component\Config\FileLocatorInterface;

final class InvoicePdfFileGenerator implements InvoicePdfFileGeneratorInterface
{
    public function __construct(
        private readonly TwigToPdfGeneratorInterface|TwigToPdfRendererInterface $twigToPdfRenderer,
        private readonly FileLocatorInterface $fileLocator,
        private readonly string $template,
        private readonly string $invoiceLogoPath,
    ) {
        if ($this->twigToPdfRenderer instanceof TwigToPdfGeneratorInterface) {
            trigger_deprecation(
                'sylius/invoicing-plugin',
                '2.2',
                'Passing an instance of %s to %s is deprecated and it will not be supported in 3.0, use an instance of %s instead.',
                TwigToPdfGeneratorInterface::class,
                self::class,
                TwigToPdfRendererInterface::class,
            );
        }
    }

    public function generate(InvoiceInterface $invoice): InvoicePdf
    {
        $filename = $invoice->path();
        $logoPath = $this->fileLocator->locate($this->invoiceLogoPath);

        $templateParams = [
            'invoice' => $invoice,
            'channel' => $invoice->channel(),
            'invoiceLogoPath' => $logoPath,
            'invoiceLogo' => $this->buildLogoDataUri($logoPath),
        ];

        if ($this->twigToPdfRenderer instanceof TwigToPdfRendererInterface) {
            $pdf = $this->twigToPdfRenderer->render($this->template, $templateParams, 'sylius_invoicing');
        } else {
            $pdf = $this->twigToPdfRenderer->generate($this->template, $templateParams);
        }

        return new InvoicePdf($filename, $pdf);
    }

    private function buildLogoDataUri(string $path): string
    {
        $mimeType = mime_content_type($path) ?: 'image/png';

        return sprintf('data:%s;base64,%s', $mimeType, base64_encode((string) file_get_contents($path)));
    }
}
