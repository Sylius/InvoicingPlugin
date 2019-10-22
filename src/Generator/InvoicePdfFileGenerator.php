<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Generator;

use Knp\Snappy\GeneratorInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Config\FileLocatorInterface;

final class InvoicePdfFileGenerator implements InvoicePdfFileGeneratorInterface
{
    private const FILE_EXTENSION = '.pdf';

    /** @var EngineInterface */
    private $templatingEngine;

    /** @var GeneratorInterface */
    private $pdfGenerator;

    /** @var FileLocatorInterface */
    private $fileLocator;

    /** @var string */
    private $template;

    /** @var string */
    private $invoiceLogoPath;

    public function __construct(
        EngineInterface $templatingEngine,
        GeneratorInterface $pdfGenerator,
        FileLocatorInterface $fileLocator,
        string $template,
        string $invoiceLogoPath
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->pdfGenerator = $pdfGenerator;
        $this->fileLocator = $fileLocator;
        $this->template = $template;
        $this->invoiceLogoPath = $invoiceLogoPath;
    }

    public function generate(InvoiceInterface $invoice): InvoicePdf
    {
        /** @var string $filename */
        $filename = str_replace('/', '_', $invoice->number()) . self::FILE_EXTENSION;

        $pdf = $this->pdfGenerator->getOutputFromHtml(
            $this->templatingEngine->render($this->template, [
                'invoice' => $invoice,
                'channel' => $invoice->channel(),
                'invoiceLogoPath' => $this->fileLocator->locate($this->invoiceLogoPath),
            ])
        );

        return new InvoicePdf($filename, $pdf);
    }
}
