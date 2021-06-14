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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Exception\InvoiceAlreadyGenerated;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;

final class InvoiceCreator implements InvoiceCreatorInterface
{
    /** @var InvoiceRepositoryInterface */
    private $invoiceRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var InvoiceGeneratorInterface */
    private $invoiceGenerator;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->orderRepository = $orderRepository;
        $this->invoiceGenerator = $invoiceGenerator;
    }

    public function __invoke(string $orderNumber, \DateTimeInterface $dateTime): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        /** @var InvoiceInterface|null $invoice */
        $invoice = $this->invoiceRepository->findOneByOrder($order);

        if (null !== $invoice) {
            throw InvoiceAlreadyGenerated::withOrderNumber($orderNumber);
        }

        $invoice = $this->invoiceGenerator->generateForOrder($order, $dateTime);

        $this->invoiceRepository->add($invoice);
    }
}
