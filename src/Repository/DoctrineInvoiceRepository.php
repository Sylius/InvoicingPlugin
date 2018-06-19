<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Sylius\InvoicingPlugin\Entity\Invoice;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

final class DoctrineInvoiceRepository implements InvoiceRepository
{
    /** @var EntityManagerInterface  */
    private $entityManager;

    /** @var EntityRepository */
    private $entityRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->entityRepository = $entityManager->getRepository(Invoice::class);
    }

    public function get(string $invoiceId): InvoiceInterface
    {
        return $this->entityRepository->find($invoiceId);
    }

    public function add(InvoiceInterface $invoice): void
    {
        $this->entityManager->persist($invoice);
        $this->entityManager->flush();
    }
}
