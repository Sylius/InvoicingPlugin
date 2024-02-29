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

namespace spec\Sylius\InvoicingPlugin\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Creator\InvoiceCreatorInterface;
use Sylius\InvoicingPlugin\Event\OrderPlaced;

final class CreateInvoiceOnOrderPlacedListenerSpec extends ObjectBehavior
{
    function let(InvoiceCreatorInterface $invoiceCreator): void
    {
        $this->beConstructedWith($invoiceCreator);
    }

    function it_requests_invoice_creation(InvoiceCreatorInterface $invoiceCreator): void
    {
        $issuedAt = new \DateTimeImmutable();

        $invoiceCreator->__invoke('0000001', $issuedAt)->shouldBeCalled();

        $this(new OrderPlaced('0000001', $issuedAt));
    }
}
