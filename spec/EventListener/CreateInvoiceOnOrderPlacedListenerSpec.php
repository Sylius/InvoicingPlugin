<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Event\OrderPlaced;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class CreateInvoiceOnOrderPlacedListenerSpec extends ObjectBehavior
{
    function let(
        InvoiceRepository $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator
    ): void {
        $this->beConstructedWith($invoiceRepository, $orderRepository, $invoiceGenerator);
    }

    function it_creates_an_invoice(
        InvoiceRepository $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator,
        OrderInterface $order,
        InvoiceInterface $invoice
    ): void {
        $issuedAt = new \DateTimeImmutable();
        $orderRepository->findOneBy(['number' => '0000001'])->willReturn($order);

        $invoiceGenerator->generateForOrder($order, $issuedAt)->willReturn($invoice);

        $invoiceRepository->add($invoice)->shouldBeCalled();

        $this(new OrderPlaced('0000001', $issuedAt));
    }
}
