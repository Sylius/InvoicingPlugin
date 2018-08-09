<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Ui\Action\Shop;

use Sylius\InvoicingPlugin\Checker\InvoiceCustomerRelationCheckerInterface;
use Sylius\InvoicingPlugin\Exception\InvoiceNotAccessible;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\ResponseBuilder\InvoiceFileResponseBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

final class DownloadInvoiceAction
{
    /** @var InvoicePdfFileGeneratorInterface */
    private $invoicePdfFileGenerator;

    /** @var InvoiceCustomerRelationCheckerInterface */
    private $invoiceCustomerRelationChecker;

    /** @var InvoiceFileResponseBuilderInterface */
    private $invoiceFileResponseBuilder;

    public function __construct(
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        InvoiceCustomerRelationCheckerInterface $invoiceCustomerRelationChecker,
        InvoiceFileResponseBuilderInterface $invoiceFileResponseBuilder
    ) {
        $this->invoicePdfFileGenerator = $invoicePdfFileGenerator;
        $this->invoiceCustomerRelationChecker = $invoiceCustomerRelationChecker;
        $this->invoiceFileResponseBuilder = $invoiceFileResponseBuilder;
    }

    public function __invoke(string $id): Response
    {
        try {
            $this->invoiceCustomerRelationChecker->check($id);
        } catch (InvoiceNotAccessible $exception) {
            return new Response($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
        }

        $invoicePdfFile = $this->invoicePdfFileGenerator->generate($id);

        return $this->invoiceFileResponseBuilder->build(Response::HTTP_OK, $invoicePdfFile);
    }
}
