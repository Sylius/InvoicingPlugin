<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Doctrine\ORM;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

interface InvoiceRepositoryInterface extends RepositoryInterface
{
    public function findOneByOrderNumber(string $orderNumber): ?InvoiceInterface;
}
