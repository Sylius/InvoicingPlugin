<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin;

use PhpSpec\ObjectBehavior;
use Ramsey\Uuid\Uuid;
use Sylius\InvoicingPlugin\InvoiceIdentifierGenerator;

final class UuidInvoiceIdentifierGeneratorSpec extends ObjectBehavior
{
    function it_is_an_invoice_identifier_generator(): void
    {
        $this->shouldImplement(InvoiceIdentifierGenerator::class);
    }

    function it_returns_a_string(): void
    {
        $this->__invoke('order id')->shouldBeString();
    }

    function it_returns_a_uuid4(): void
    {
        Uuid::fromString($this->__invoke('order id')->getWrappedObject());
    }

    function it_returns_two_different_strings_on_subsequent_calls(): void
    {
        $this->__invoke('order id')->shouldNotReturn($this->__invoke('order id')->getWrappedObject());
    }
}
