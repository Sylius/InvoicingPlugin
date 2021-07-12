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
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Saver\InvoiceFileSaverInterface;

final class InvoiceCreator implements InvoiceCreatorInterface
{
    /** @var InvoiceRepositoryInterface */
    private $invoiceRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var InvoiceGeneratorInterface */
    private $invoiceGenerator;

    /** @var InvoicePdfFileGeneratorInterface */
    private $invoicePdfFileGenerator;

    /** @var InvoiceFileSaverInterface */
    private $invoiceFileSaver;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        InvoiceFileSaverInterface $invoiceFileSaver
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->orderRepository = $orderRepository;
        $this->invoiceGenerator = $invoiceGenerator;
        $this->invoicePdfFileGenerator = $invoicePdfFileGenerator;
        $this->invoiceFileSaver = $invoiceFileSaver;
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
        $invoicePdf = $this->invoicePdfFileGenerator->generate($invoice);
        $this->invoiceFileSaver->save($invoicePdf);

        $this->invoiceRepository->add($invoice);
    }
}
