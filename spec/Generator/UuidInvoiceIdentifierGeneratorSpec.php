<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Generator\InvoiceIdentifierGenerator;

final class UuidInvoiceIdentifierGeneratorSpec extends ObjectBehavior
{
    function it_is_an_invoice_identifier_generator(): void
    {
        $this->shouldImplement(InvoiceIdentifierGenerator::class);
    }

    function it_returns_a_string(): void
    {
        $this->generate()->shouldBeString();
    }

    function it_returns_two_different_strings_on_subsequent_calls(): void
    {
        $this->generate()->shouldNotReturn($this->generate());
    }
}
