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

namespace Tests\Sylius\InvoicingPlugin\Unit\EventListener;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\InvoicingPlugin\Creator\InvoiceCreatorInterface;
use Sylius\InvoicingPlugin\Event\OrderPlaced;
use Sylius\InvoicingPlugin\EventListener\CreateInvoiceOnOrderPlacedListener;

final class CreateInvoiceOnOrderPlacedListenerTest extends TestCase
{
    #[Test]
    public function it_requests_invoice_creation(): void
    {
        $invoiceCreator = $this->createMock(InvoiceCreatorInterface::class);
        $issuedAt = new \DateTimeImmutable();

        $invoiceCreator
            ->expects(self::once())
            ->method('__invoke')
            ->with('0000001', $issuedAt);

        $listener = new CreateInvoiceOnOrderPlacedListener($invoiceCreator);
        $listener(new OrderPlaced('0000001', $issuedAt));
    }
}
