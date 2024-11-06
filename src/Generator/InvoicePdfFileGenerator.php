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
use Symfony\Component\Config\FileLocatorInterface;

final class InvoicePdfFileGenerator implements InvoicePdfFileGeneratorInterface
{
    public function __construct(
        private readonly TwigToPdfGeneratorInterface $twigToPdfGenerator,
        private readonly FileLocatorInterface $fileLocator,
        private readonly InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator,
        private readonly string $template,
        private readonly string $invoiceLogoPath,
    ) {
    }

    public function generate(InvoiceInterface $invoice): InvoicePdf
    {
        $filename = $this->invoiceFileNameGenerator->generateForPdf($invoice);

        $pdf = $this->twigToPdfGenerator->generate(
            $this->template,
            [
                'invoice' => $invoice,
                'channel' => $invoice->channel(),
                'invoiceLogoPath' => $this->fileLocator->locate($this->invoiceLogoPath),
            ],
        );

        return new InvoicePdf($filename, $pdf);
    }
}
