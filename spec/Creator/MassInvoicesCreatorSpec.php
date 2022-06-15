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

namespace spec\Sylius\InvoicingPlugin\Creator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\InvoicingPlugin\Creator\InvoiceCreatorInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;

final class MassInvoicesCreatorSpec extends ObjectBehavior
{
    function let(
        InvoiceCreatorInterface $invoiceCreator,
        DateTimeProvider $dateTimeProvider
    ): void {
        $this->beConstructedWith($invoiceCreator, $dateTimeProvider);
    }

    function it_requests_invoices_creation_for_multiple_orders(
        InvoiceCreatorInterface $invoiceCreator,
        DateTimeProvider $dateTimeProvider,
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        OrderInterface $thirdOrder
    ): void {
        $firstOrder->getId()->willReturn(1);
        $secondOrder->getId()->willReturn(2);
        $thirdOrder->getId()->willReturn(3);

        $firstInvoiceDateTime = new \DateTimeImmutable('2019-02-25');
        $secondInvoiceDateTime = new \DateTimeImmutable('2019-02-25');
        $thirdInvoiceDateTime = new \DateTimeImmutable('2019-02-25');

        $dateTimeProvider->__invoke()->willReturn($firstInvoiceDateTime, $secondInvoiceDateTime, $thirdInvoiceDateTime);

        $invoiceCreator->__invoke(1, $firstInvoiceDateTime)->shouldBeCalled();
        $invoiceCreator->__invoke(2, $secondInvoiceDateTime)->shouldBeCalled();
        $invoiceCreator->__invoke(3, $thirdInvoiceDateTime)->shouldBeCalled();

        $this->__invoke([$firstOrder->getWrappedObject(), $secondOrder->getWrappedObject(), $thirdOrder->getWrappedObject()]);
    }
}
