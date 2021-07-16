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

namespace spec\Sylius\InvoicingPlugin\Command;

use PhpSpec\ObjectBehavior;

final class SendInvoiceEmailSpec extends ObjectBehavior
{
    function it_represents_an_intention_to_send_email_containing_invoice(): void
    {
        $this->beConstructedWith('0000001');

        $this->orderNumber()->shouldReturn('0000001');
    }
}
