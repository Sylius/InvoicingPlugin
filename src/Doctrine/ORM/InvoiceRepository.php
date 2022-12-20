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

namespace Sylius\InvoicingPlugin\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Webmozart\Assert\Assert;

class InvoiceRepository extends EntityRepository implements InvoiceRepositoryInterface
{
    public function findOneByOrder(OrderInterface $order): ?InvoiceInterface
    {
        /** @var InvoiceInterface|null $invoice */
        $invoice = $this->findOneBy(['order' => $order]);

        return $invoice;
    }

    public function findByOrderNumber(string $orderNumber): array
    {
        $invoices = $this
            ->createQueryBuilder('invoice')
            ->innerJoin('invoice.order', 'o')
            ->where('o.number = :orderNumber')
            ->setParameter('orderNumber', $orderNumber)
            ->getQuery()
            ->getResult()
        ;

        Assert::isArray($invoices);

        return $invoices;
    }
}
