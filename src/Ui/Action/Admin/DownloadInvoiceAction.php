<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Ui\Action\Admin;

use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\ResponseBuilder\InvoiceFileResponseBuilder;
use Sylius\InvoicingPlugin\ResponseBuilder\InvoiceFileResponseBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

final class DownloadInvoiceAction
{
    /** @var InvoicePdfFileGeneratorInterface */
    private $invoicePdfFileGenerator;

    /** @var InvoiceFileResponseBuilderInterface */
    private $invoiceFileResponseBuilder;

    public function __construct(
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        InvoiceFileResponseBuilder $invoiceFileResponseBuilder
    ) {
        $this->invoicePdfFileGenerator = $invoicePdfFileGenerator;
        $this->invoiceFileResponseBuilder = $invoiceFileResponseBuilder;
    }

    public function __invoke(string $id): Response
    {
        $invoicePdfFile = $this->invoicePdfFileGenerator->generate($id);

        return $this->invoiceFileResponseBuilder->build(Response::HTTP_OK, $invoicePdfFile);
    }
}
