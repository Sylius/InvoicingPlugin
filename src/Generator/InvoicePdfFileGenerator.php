<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Generator;

use Knp\Snappy\GeneratorInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Config\FileLocatorInterface;

final class InvoicePdfFileGenerator implements InvoicePdfFileGeneratorInterface
{
    private const FILE_EXTENSION = '.pdf';

    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

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
        InvoiceRepository $invoiceRepository,
        ChannelRepositoryInterface $channelRepository,
        EngineInterface $templatingEngine,
        GeneratorInterface $pdfGenerator,
        FileLocatorInterface $fileLocator,
        string $template,
        string $invoiceLogoPath
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->channelRepository = $channelRepository;
        $this->templatingEngine = $templatingEngine;
        $this->pdfGenerator = $pdfGenerator;
        $this->fileLocator = $fileLocator;
        $this->template = $template;
        $this->invoiceLogoPath = $invoiceLogoPath;
    }

    public function generate(string $invoiceId): InvoicePdf
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $this->invoiceRepository->get($invoiceId);

        $channel = $this->channelRepository->findOneByCode($invoice->channel()->getCode());

        $filename = str_replace('/', '_', $invoice->number()) . self::FILE_EXTENSION;

        $pdf = $this->pdfGenerator->getOutputFromHtml(
            $this->templatingEngine->render($this->template, [
                'invoice' => $invoice,
                'channel' => $channel,
                'invoiceLogoPath' => $this->fileLocator->locate($this->invoiceLogoPath),
            ])
        );

        return new InvoicePdf($filename, $pdf);
    }
}
