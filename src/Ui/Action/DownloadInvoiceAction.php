<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Ui\Action;

use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Provider\InvoiceFileProviderInterface;
use Sylius\InvoicingPlugin\Security\Voter\InvoiceVoter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Webmozart\Assert\Assert;

final class DownloadInvoiceAction
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $invoiceRepository,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly InvoiceFileProviderInterface $invoiceFilePathProvider,
        private readonly bool $hasEnabledPdfFileGenerator = true,
    ) {
    }

    public function __invoke(string $id): Response
    {
        if (!$this->hasEnabledPdfFileGenerator) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        /** @var InvoiceInterface|null $invoice */
        $invoice = $this->invoiceRepository->find($id);
        Assert::notNull($invoice);

        if (!$this->authorizationChecker->isGranted(InvoiceVoter::ACCESS, $invoice)) {
            throw new AccessDeniedHttpException();
        }

        $invoiceFile = $this->invoiceFilePathProvider->provide($invoice);

        $response = new Response($invoiceFile->content(), Response::HTTP_OK, ['Content-Type' => 'application/pdf']);
        $response->headers->add([
            'Content-Disposition' => $response->headers->makeDisposition('attachment', $invoiceFile->filename()),
        ]);

        return $response;
    }
}
