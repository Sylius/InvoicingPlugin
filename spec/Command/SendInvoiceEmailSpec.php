<?php

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
