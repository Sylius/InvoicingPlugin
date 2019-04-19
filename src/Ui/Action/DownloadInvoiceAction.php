<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Ui\Action;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
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

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    /** @var InvoicePdfFileGeneratorInterface */
    private $invoicePdfFileGenerator;

    public function __construct(
        InvoiceRepository $invoiceRepository,
        ChannelRepositoryInterface $channelRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->channelRepository = $channelRepository;
        $this->authorizationChecker = $authorizationChecker;
        $this->invoicePdfFileGenerator = $invoicePdfFileGenerator;
    }

    public function __invoke(string $id): Response
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $this->invoiceRepository->get($id);

        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($invoice->channel()->getCode());

        if (!$this->authorizationChecker->isGranted(InvoiceVoter::ACCESS, $invoice)) {
            throw new AccessDeniedHttpException();
        }

        $invoicePdf = $this->invoicePdfFileGenerator->generate($invoice, $channel);

        $response = new Response($invoicePdf->content(), Response::HTTP_OK, ['Content-Type' => 'application/pdf']);
        $response->headers->add([
            'Content-Disposition' => $response->headers->makeDisposition('attachment', $invoicePdf->filename()),
        ]);

        return $response;
    }
}
