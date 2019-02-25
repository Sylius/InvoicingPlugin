<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Creator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Sylius\InvoicingPlugin\Repository\InvoiceRepository;

final class InvoicesForPreviouslyPlacedOrdersCreatorSpec extends ObjectBehavior
{
    public function let(
        InvoiceRepository $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceGeneratorInterface $invoiceGenerator,
        DateTimeProvider $dateTimeProvider
    ): void {
        $this->beConstructedWith($invoiceRepository, $orderRepository, $invoiceGenerator, $dateTimeProvider);
    }

    public function it_creates_invoices_for_previously_placed_orders(
        OrderRepositoryInterface $orderRepository,
        InvoiceRepository $invoiceRepository,
        InvoiceGeneratorInterface $invoiceGenerator,
        DateTimeProvider $dateTimeProvider,
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        OrderInterface $thirdOrder,
        InvoiceInterface $invoiceForFirstOrder,
        InvoiceInterface $invoiceForSecondOrder,
        InvoiceInterface $invoiceForThirdOrder
    ): void {
        $firstOrder->getNumber()->willReturn('0000001');
        $secondOrder->getNumber()->willReturn('0000002');
        $thirdOrder->getNumber()->willReturn('0000003');

        $orderRepository->findAll()->willReturn([$firstOrder, $secondOrder, $thirdOrder]);

        $invoiceRepository->findOneByOrderNumber('0000001')->willReturn(null);
        $invoiceRepository->findOneByOrderNumber('0000002')->willReturn(null);
        $invoiceRepository->findOneByOrderNumber('0000003')->willReturn($invoiceForThirdOrder);

        $firstInvoiceDateTime = new \DateTimeImmutable('2019-02-25');
        $secondInvoiceDateTime = new \DateTimeImmutable('2019-02-25');

        $dateTimeProvider->__invoke()->willReturn($firstInvoiceDateTime, $secondInvoiceDateTime);

        $invoiceGenerator->generateForOrder($firstOrder, $firstInvoiceDateTime)->willReturn($invoiceForFirstOrder);
        $invoiceGenerator->generateForOrder($secondOrder, $secondInvoiceDateTime)->willReturn($invoiceForSecondOrder);
        $invoiceGenerator->generateForOrder($thirdOrder, Argument::type(\DateTimeImmutable::class))->shouldNotBeCalled();

        $invoiceRepository->add($invoiceForFirstOrder)->shouldBeCalled();
        $invoiceRepository->add($invoiceForSecondOrder)->shouldBeCalled();

        $this->__invoke([$firstOrder->getWrappedObject(), $secondOrder->getWrappedObject(), $thirdOrder->getWrappedObject()]);
    }
}
