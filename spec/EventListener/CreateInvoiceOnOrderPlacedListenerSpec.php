<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Entity\Invoice;
use Sylius\InvoicingPlugin\Event\OrderPlaced;
use Sylius\InvoicingPlugin\InvoiceIdentifierGenerator;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class CreateInvoiceOnOrderPlacedListenerSpec extends ObjectBehavior
{
    function let(
        InvoiceRepository $invoiceRepository,
        InvoiceIdentifierGenerator $invoiceIdentifierGenerator
    ): void {
        $this->beConstructedWith($invoiceRepository, $invoiceIdentifierGenerator);
    }

    function it_creates_an_invoice(
        InvoiceRepository $invoiceRepository,
        InvoiceIdentifierGenerator $invoiceIdentifierGenerator
    ): void {
        $issuedAt = new \DateTimeImmutable('now');

        $invoiceIdentifierGenerator->__invoke('007')->willReturn('007/1337');

        $invoiceRepository->add(new Invoice('007/1337', '007', $issuedAt))->shouldBeCalled();

        $this(new OrderPlaced('007', $issuedAt));
    }
}
