<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Repository;

@trigger_error('The "DoctrineInvoiceRepository" class is deprecated since version 1.0.0 Use standardized class located at "src/Doctrine/ORM/" instead.');

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Webmozart\Assert\Assert;

final class DoctrineInvoiceRepository extends EntityRepository implements InvoiceRepository
{
    public function get(string $invoiceId): InvoiceInterface
    {
        /** @var InvoiceInterface|null $invoice */
        $invoice = $this->find($invoiceId);
        Assert::notNull($invoice);

        return $invoice;
    }

    public function findOneByOrderNumber(string $orderNumber): ?InvoiceInterface
    {
        /** @var InvoiceInterface|null $invoice */
        $invoice = $this->findOneBy(['orderNumber' => $orderNumber]);

        return $invoice;
    }
}
