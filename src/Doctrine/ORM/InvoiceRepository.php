<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

final class InvoiceRepository extends EntityRepository implements InvoiceRepositoryInterface
{
    public function findOneByOrderNumber(string $orderNumber): ?InvoiceInterface
    {
        /** @var InvoiceInterface|null $invoice */
        $invoice = $this->findOneBy(['orderNumber' => $orderNumber]);

        return $invoice;
    }
}
