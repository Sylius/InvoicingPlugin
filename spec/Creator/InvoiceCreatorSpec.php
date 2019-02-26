<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Creator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Creator\InvoiceCreatorInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Exception\InvoiceAlreadyGenerated;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class InvoiceCreatorSpec extends ObjectBehavior
{
    public function let(
        InvoiceRepository $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator
    ): void {
        $this->beConstructedWith($invoiceRepository, $orderRepository, $invoiceGenerator);
    }

    public function it_implements_invoice_for_order_creator_interface(): void
    {
        $this->shouldImplement(InvoiceCreatorInterface::class);
    }

    public function it_creates_invoice_for_order(
        InvoiceRepository $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator,
        OrderInterface $order,
        InvoiceInterface $invoice
    ): void {
        $invoiceRepository->findOneByOrderNumber('0000001')->willReturn(null);

        $orderRepository->findOneByNumber('0000001')->willReturn($order);

        $invoiceDateTime = new \DateTimeImmutable('2019-02-25');

        $invoiceGenerator->generateForOrder($order, $invoiceDateTime)->willReturn($invoice);

        $invoiceRepository->add($invoice)->shouldBeCalled();

        $this->__invoke('0000001', $invoiceDateTime);
    }

    public function it_throws_an_exception_when_invoice_was_already_created_for_given_order(
        InvoiceRepository $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator,
        InvoiceInterface $invoice
    ): void {
        $invoiceRepository->findOneByOrderNumber('0000001')->willReturn($invoice);

        $invoiceDateTime = new \DateTimeImmutable('2019-02-25');

        $orderRepository->findOneByNumber(Argument::any())->shouldNotBeCalled();
        $invoiceGenerator->generateForOrder(Argument::any(), Argument::any())->shouldNotBeCalled();
        $invoiceRepository->add(Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(InvoiceAlreadyGenerated::withOrderNumber('0000001'))
            ->during('__invoke', ['0000001', $invoiceDateTime])
        ;
    }
}
