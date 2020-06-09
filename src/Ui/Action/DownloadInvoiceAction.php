<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Ui\Action;

use League\Flysystem\FilesystemInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;
use Sylius\InvoicingPlugin\Security\Voter\InvoiceVoter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class DownloadInvoiceAction
{
    /** @var InvoiceRepository */
    private $invoiceRepository;

    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    /** @var InvoicePdfFileGeneratorInterface */
    private $invoicePdfFileGenerator;

    /** @var FilesystemInterface */
    private $invoiceFilesystem;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        FilesystemInterface $invoiceFilesystem
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->authorizationChecker = $authorizationChecker;
        $this->invoicePdfFileGenerator = $invoicePdfFileGenerator;
        $this->invoiceFilesystem = $invoiceFilesystem;
    }

    public function __invoke(string $id): Response
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $this->invoiceRepository->get($id);

        if (!$this->authorizationChecker->isGranted(InvoiceVoter::ACCESS, $invoice)) {
            throw new AccessDeniedHttpException();
        }

        $pdfInvoiceFilename = $this->invoicePdfFileGenerator->buildFilenameForInvoice($invoice);

        if (!$this->invoiceFilesystem->has($pdfInvoiceFilename)) {
            $pdfInvoice = $this->invoicePdfFileGenerator->generate($invoice);

            $this->invoiceFilesystem->put(
                $pdfInvoice->filename(),
                $pdfInvoice->content()
            );
        }

        $pdfInvoiceFile = $this->invoiceFilesystem->read($pdfInvoiceFilename);

        $response = new Response($pdfInvoiceFile, Response::HTTP_OK, ['Content-Type' => 'application/pdf']);
        $response->headers->add([
            'Content-Disposition' => $response->headers->makeDisposition('attachment', $pdfInvoiceFilename),
        ]);

        return $response;
    }
}
