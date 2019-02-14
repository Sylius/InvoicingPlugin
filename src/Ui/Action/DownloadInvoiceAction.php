<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Ui\Action;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Security\Voter\InvoiceVoter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class DownloadInvoiceAction
{
    /** @var RepositoryInterface */
    private $invoiceRepository;

    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    /** @var InvoicePdfFileGeneratorInterface */
    private $invoicePdfFileGenerator;

    public function __construct(
        RepositoryInterface $invoiceRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->authorizationChecker = $authorizationChecker;
        $this->invoicePdfFileGenerator = $invoicePdfFileGenerator;
    }

    public function __invoke(string $id): Response
    {
        $invoice = $this->invoiceRepository->find($id);

        if (!$this->authorizationChecker->isGranted(InvoiceVoter::ACCESS, $invoice)) {
            throw new AccessDeniedHttpException();
        }

        $invoicePdf = $this->invoicePdfFileGenerator->generate($id);

        $response = new Response($invoicePdf->content(), Response::HTTP_OK, ['Content-Type' => 'application/pdf']);
        $response->headers->add([
            'Content-Disposition' => $response->headers->makeDisposition('attachment', $invoicePdf->filename()),
        ]);

        return $response;
    }
}
