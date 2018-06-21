<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Ui\Action;

use Knp\Snappy\GeneratorInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class DownloadInvoiceAction
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var EngineInterface */
    private $templatingEngine;

    /** @var GeneratorInterface */
    private $pdfGenerator;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        EngineInterface $templatingEngine,
        GeneratorInterface $pdfGenerator
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->templatingEngine = $templatingEngine;
        $this->pdfGenerator = $pdfGenerator;
    }

    public function __invoke(Request $request, string $id): Response
    {
        $filename = sprintf('invoice-%s.pdf', $id);

        $response = new Response($this->pdfGenerator->getOutputFromHtml(
            $this->templatingEngine->render('@SyliusInvoicingPlugin/Resources/views/Invoice/Download/pdf.html.twig', [
                'invoice' => $this->invoiceRepository->get($id),
            ])
        ));

        $response->headers->add(['Content-Type' => 'application/pdf']);
        $response->headers->add(['Content-Disposition' => $response->headers->makeDisposition('attachment', $filename)]);

        return $response;
    }
}
