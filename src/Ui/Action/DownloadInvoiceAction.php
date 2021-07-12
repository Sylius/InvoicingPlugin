<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Ui\Action;

use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Provider\InvoiceFilePathProviderInterface;
use Sylius\InvoicingPlugin\Security\Voter\InvoiceVoter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Webmozart\Assert\Assert;

final class DownloadInvoiceAction
{
    /** @var InvoiceRepositoryInterface */
    private $invoiceRepository;

    /** @var AuthorizationCheckerInterface */
    private $authorizationChecker;

    /** @var InvoiceFilePathProviderInterface */
    private $invoiceFilePathProvider;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        InvoiceFilePathProviderInterface $invoiceFilePathProvider
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->authorizationChecker = $authorizationChecker;
        $this->invoiceFilePathProvider = $invoiceFilePathProvider;
    }

    public function __invoke(string $id): Response
    {
        /** @var InvoiceInterface|null $invoice */
        $invoice = $this->invoiceRepository->find($id);
        Assert::notNull($invoice);

        if (!$this->authorizationChecker->isGranted(InvoiceVoter::ACCESS, $invoice)) {
            throw new AccessDeniedHttpException();
        }

        $invoiceFilePath = $this->invoiceFilePathProvider->provide($invoice);

        $invoiceFilePathSplit = explode('/', $invoiceFilePath);
        $invoiceFileName = $invoiceFilePathSplit[count($invoiceFilePathSplit) - 1];

        $response = new Response((string) file_get_contents($invoiceFilePath), Response::HTTP_OK, ['Content-Type' => 'application/pdf']);
        $response->headers->add([
            'Content-Disposition' => $response->headers->makeDisposition('attachment', $invoiceFileName),
        ]);

        return $response;
    }
}
