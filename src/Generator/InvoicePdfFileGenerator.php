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

namespace Sylius\InvoicingPlugin\Generator;

use Knp\Snappy\GeneratorInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Symfony\Component\Config\FileLocatorInterface;
use Twig\Environment;

final class InvoicePdfFileGenerator implements InvoicePdfFileGeneratorInterface
{
    public function __construct(
        private Environment $templatingEngine,
        private GeneratorInterface $pdfGenerator,
        private PdfOptionsGeneratorInterface $pdfOptionsGenerator,
        private FileLocatorInterface $fileLocator,
        private InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator,
        private string $template,
        private string $invoiceLogoPath
    ) {
    }

    public function generate(InvoiceInterface $invoice): InvoicePdf
    {
        $filename = $this->invoiceFileNameGenerator->generateForPdf($invoice);

        $pdf = $this->pdfGenerator->getOutputFromHtml(
            $this->templatingEngine->render($this->template, [
                'invoice' => $invoice,
                'channel' => $invoice->channel(),
                'invoiceLogoPath' => $this->fileLocator->locate($this->invoiceLogoPath),
            ]),
            $this->pdfOptionsGenerator->generate()
        );

        return new InvoicePdf($filename, $pdf);
    }
}
