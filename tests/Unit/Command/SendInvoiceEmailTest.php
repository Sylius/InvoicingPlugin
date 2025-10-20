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

namespace Tests\Sylius\InvoicingPlugin\Unit\Command;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\InvoicingPlugin\Command\SendInvoiceEmail;

final class SendInvoiceEmailTest extends TestCase
{
    #[Test]
    public function it_represents_an_intention_to_send_email_containing_invoice(): void
    {
        $command = new SendInvoiceEmail('0000001');

        self::assertSame('0000001', $command->orderNumber());
    }
}
