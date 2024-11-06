<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Creator;

use Doctrine\ORM\Exception\ORMException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Creator\InvoiceCreatorInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Exception\InvoiceAlreadyGenerated;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Generator\InvoicePdfFileGeneratorInterface;
use Sylius\InvoicingPlugin\Manager\InvoiceFileManagerInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;

final class InvoiceCreatorSpec extends ObjectBehavior
{
    function let(
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        InvoiceFileManagerInterface $invoiceFileManager,
    ): void {
        $this->beConstructedWith(
            $invoiceRepository,
            $orderRepository,
            $invoiceGenerator,
            $invoicePdfFileGenerator,
            $invoiceFileManager,
        );
    }

    function it_implements_invoice_for_order_creator_interface(): void
    {
        $this->shouldImplement(InvoiceCreatorInterface::class);
    }

    function it_creates_invoice_for_order(
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        InvoiceFileManagerInterface $invoiceFileManager,
        OrderInterface $order,
        InvoiceInterface $invoice,
    ): void {
        $invoicePdf = new InvoicePdf('invoice.pdf', 'CONTENT');

        $orderRepository->findOneByNumber('0000001')->willReturn($order);

        $invoiceRepository->findOneByOrder($order)->willReturn(null);

        $invoiceDateTime = new \DateTimeImmutable('2019-02-25');

        $invoiceGenerator->generateForOrder($order, $invoiceDateTime)->willReturn($invoice);
        $invoicePdfFileGenerator->generate($invoice)->willReturn($invoicePdf);
        $invoiceFileManager->save($invoicePdf)->shouldBeCalled();

        $invoiceRepository->add($invoice)->shouldBeCalled();

        $this->__invoke('0000001', $invoiceDateTime);
    }

    function it_creates_invoice_without_generating_pdf_file(
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        InvoiceFileManagerInterface $invoiceFileManager,
        OrderInterface $order,
        InvoiceInterface $invoice,
    ): void {
        $this->beConstructedWith(
            $invoiceRepository,
            $orderRepository,
            $invoiceGenerator,
            $invoicePdfFileGenerator,
            $invoiceFileManager,
            false,
        );

        $orderRepository->findOneByNumber('0000001')->willReturn($order);

        $invoiceRepository->findOneByOrder($order)->willReturn(null);

        $invoiceDateTime = new \DateTimeImmutable('2019-02-25');

        $invoiceGenerator->generateForOrder($order, $invoiceDateTime)->willReturn($invoice);

        $invoicePdfFileGenerator->generate($invoice)->shouldNotBeCalled();
        $invoiceFileManager->save(Argument::any())->shouldNotBeCalled();

        $invoiceRepository->add($invoice)->shouldBeCalled();

        $this('0000001', $invoiceDateTime);
    }

    function it_removes_saved_invoice_file_if_database_update_fails(
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator,
        InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
        InvoiceFileManagerInterface $invoiceFileManager,
        OrderInterface $order,
        InvoiceInterface $invoice,
    ): void {
        $invoicePdf = new InvoicePdf('invoice.pdf', 'CONTENT');

        $orderRepository->findOneByNumber('0000001')->willReturn($order);

        $invoiceRepository->findOneByOrder($order)->willReturn(null);

        $invoiceDateTime = new \DateTimeImmutable('2019-02-25');

        $invoiceGenerator->generateForOrder($order, $invoiceDateTime)->willReturn($invoice);
        $invoicePdfFileGenerator->generate($invoice)->willReturn($invoicePdf);
        $invoiceFileManager->save($invoicePdf)->shouldBeCalled();

        $invoiceRepository->add($invoice)->willThrow(ORMException::class);
        $invoiceFileManager->remove($invoicePdf)->shouldBeCalled();

        $this->__invoke('0000001', $invoiceDateTime);
    }

    function it_throws_an_exception_when_invoice_was_already_created_for_given_order(
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator,
        OrderInterface $order,
        InvoiceInterface $invoice,
    ): void {
        $orderRepository->findOneByNumber('0000001')->willReturn($order);
        $invoiceRepository->findOneByOrder($order)->willReturn($invoice);

        $invoiceDateTime = new \DateTimeImmutable('2019-02-25');

        $invoiceGenerator->generateForOrder(Argument::any(), Argument::any())->shouldNotBeCalled();
        $invoiceRepository->add(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(InvoiceAlreadyGenerated::withOrderNumber('0000001'))
            ->during('__invoke', ['0000001', $invoiceDateTime])
        ;
    }
}
