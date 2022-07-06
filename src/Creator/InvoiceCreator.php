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

namespace Sylius\InvoicingPlugin\Creator;

use Webmozart\Assert\Assert;
use Doctrine\ORM\ORMException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Exception\InvoiceAlreadyGenerated;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Manager\InvoiceFileManagerInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;

final class InvoiceCreator implements InvoiceCreatorInterface
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private OrderRepositoryInterface $orderRepository,
        private InvoiceGeneratorInterface $invoiceGenerator,
        private InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        private InvoiceFileManagerInterface $invoiceFileManager,
        private bool $hasEnabledPdfFileGenerator = true
    ) {
    }

    public function __invoke(string $orderNumber, \DateTimeInterface $dateTime): void
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['number' => $orderNumber]);
        Assert::notNull($order, sprintf('Order with number "%s" does not exist.', $orderNumber));

        /** @var InvoiceInterface|null $invoice */
        $invoice = $this->invoiceRepository->findOneByOrder($order);

        if (null !== $invoice) {
            throw InvoiceAlreadyGenerated::withOrderNumber($orderNumber);
        }

        $invoice = $this->invoiceGenerator->generateForOrder($order, $dateTime);

        if (!$this->hasEnabledPdfFileGenerator) {
            $this->invoiceRepository->add($invoice);

            return;
        }

        $invoicePdf = $this->invoicePdfFileGenerator->generate($invoice);
        $this->invoiceFileManager->save($invoicePdf);

        try {
            $this->invoiceRepository->add($invoice);
        } catch (ORMException $exception) {
            $this->invoiceFileManager->remove($invoicePdf);
        }
    }
}
