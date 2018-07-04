<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Repository;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\InvoicingPlugin\Entity\Invoice;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Webmozart\Assert\Assert;

final class DoctrineInvoiceRepository implements InvoiceRepository
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ObjectRepository */
    private $entityRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityRepository = $entityManager->getRepository(Invoice::class);
    }

    public function get(string $invoiceId): InvoiceInterface
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $this->entityRepository->find($invoiceId);

        return $invoice;
    }

    public function add(InvoiceInterface $invoice): void
    {
        $this->entityManager->persist($invoice);
        $this->entityManager->flush();
    }

    public function getOneByOrderNumber(string $orderNumber): InvoiceInterface
    {
        /** @var InvoiceInterface $invoice */
        $invoice = $this->entityRepository->findOneBy(['orderNumber' => $orderNumber]);

        Assert::notNull($invoice, InvoiceInterface::class);

        return $invoice;
    }
}
