<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Generator;

use Knp\Snappy\GeneratorInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

final class InvoicePdfFileGenerator implements InvoicePdfFileGeneratorInterface
{
    private const FILE_EXTENSION = '.pdf';

    /** @var RepositoryInterface */
    private $invoiceRepository;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var EngineInterface */
    private $templatingEngine;

    /** @var GeneratorInterface */
    private $pdfGenerator;

    /** @var string */
    private $template;

    public function __construct(
        RepositoryInterface $invoiceRepository,
        ChannelRepositoryInterface $channelRepository,
        EngineInterface $templatingEngine,
        GeneratorInterface $pdfGenerator,
        string $template
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->channelRepository = $channelRepository;
        $this->templatingEngine = $templatingEngine;
        $this->pdfGenerator = $pdfGenerator;
        $this->template = $template;
    }

    public function generate(string $invoiceId): InvoicePdf
    {
        $invoice = $this->invoiceRepository->find($invoiceId);

        $channel = $this->channelRepository->findOneByCode($invoice->channel()->getCode());

        $filename = str_replace('/', '_', $invoice->number()) . self::FILE_EXTENSION;

        $pdf = $this->pdfGenerator->getOutputFromHtml(
            $this->templatingEngine->render($this->template, ['invoice' => $invoice, 'channel' => $channel])
        );

        return new InvoicePdf($filename, $pdf);
    }
}
