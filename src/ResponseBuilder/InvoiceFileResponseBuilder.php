<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\ResponseBuilder;

use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Symfony\Component\HttpFoundation\Response;

final class InvoiceFileResponseBuilder implements InvoiceFileResponseBuilderInterface
{
    public function build(int $status, InvoicePdf $invoicePdf): Response
    {
        $response = new Response($invoicePdf->content(), $status, ['Content-Type' => 'application/pdf']);
        $response->headers->add([
            'Content-Disposition' => $response->headers->makeDisposition('attachment', $invoicePdf->filename()),
        ]);

        return $response;
    }
}
