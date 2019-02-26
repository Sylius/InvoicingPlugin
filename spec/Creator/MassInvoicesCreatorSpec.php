<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Creator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Creator\InvoiceCreatorInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;

final class MassInvoicesCreatorSpec extends ObjectBehavior
{
    public function let(
        InvoiceCreatorInterface $invoiceCreator,
        DateTimeProvider $dateTimeProvider
    ): void {
        $this->beConstructedWith($invoiceCreator, $dateTimeProvider);
    }

    public function it_requests_invoices_creation_for_multiple_orders(
        InvoiceCreatorInterface $invoiceCreator,
        DateTimeProvider $dateTimeProvider,
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        OrderInterface $thirdOrder
    ): void {
        $firstOrder->getNumber()->willReturn('0000001');
        $secondOrder->getNumber()->willReturn('0000002');
        $thirdOrder->getNumber()->willReturn('0000003');

        $firstInvoiceDateTime = new \DateTimeImmutable('2019-02-25');
        $secondInvoiceDateTime = new \DateTimeImmutable('2019-02-25');
        $thirdInvoiceDateTime = new \DateTimeImmutable('2019-02-25');

        $dateTimeProvider->__invoke()->willReturn($firstInvoiceDateTime, $secondInvoiceDateTime, $thirdInvoiceDateTime);

        $invoiceCreator->__invoke('0000001', $firstInvoiceDateTime)->shouldBeCalled();
        $invoiceCreator->__invoke('0000002', $secondInvoiceDateTime)->shouldBeCalled();
        $invoiceCreator->__invoke('0000003', $thirdInvoiceDateTime)->shouldBeCalled();

        $this->__invoke([$firstOrder->getWrappedObject(), $secondOrder->getWrappedObject(), $thirdOrder->getWrappedObject()]);
    }
}
