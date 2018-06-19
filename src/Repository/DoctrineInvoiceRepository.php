<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\InvoicingPlugin\Entity\Invoice;

final class DoctrineInvoiceRepository implements InvoiceRepository
{
    /** @var EntityManagerInterface  */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function add(Invoice $invoice): void
    {
        $this->entityManager->persist($invoice);
        $this->entityManager->flush();
    }
}
