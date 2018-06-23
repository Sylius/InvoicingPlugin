<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Event\OrderPlaced;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class CreateInvoiceOnOrderPlacedListenerSpec extends ObjectBehavior
{
    function let(
        InvoiceRepository $invoiceRepository,
        InvoiceGeneratorInterface $invoiceGenerator
    ): void {
        $this->beConstructedWith($invoiceRepository, $invoiceGenerator);
    }

    function it_creates_an_invoice(
        InvoiceRepository $invoiceRepository,
        InvoiceGeneratorInterface $invoiceGenerator,
        OrderInterface $order,
        InvoiceInterface $invoice
    ): void {
        $issuedAt = new \DateTimeImmutable('now');

        $invoiceGenerator->generateForOrder($order, $issuedAt)->willReturn($invoice);

        $invoiceRepository->add($invoice)->shouldBeCalled();

        $this(new OrderPlaced($order->getWrappedObject(), $issuedAt));
    }
}
