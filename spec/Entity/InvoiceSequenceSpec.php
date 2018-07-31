<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Entity\InvoiceSequenceInterface;

final class InvoiceSequenceSpec extends ObjectBehavior
{
    function it_implements_an_invoice_sequence_interface(): void
    {
        $this->shouldImplement(InvoiceSequenceInterface::class);
    }

    function it_has_zero_index_after_initialized(): void
    {
        $this->getIndex()->shouldReturn(0);
    }

    function it_increments_index(): void
    {
        $this->incrementIndex();
        $this->getIndex()->shouldReturn(1);
    }

    function it_can_reset_index(): void
    {
        $this->resetIndex();
        $this->getIndex()->shouldReturn(0);
    }

    function it_has_version_1_by_default(): void
    {
        $this->getVersion()->shouldReturn(1);
    }

    function it_has_last_generated_at_mutable(\DateTime $lastGeneratedAt): void
    {
        $this->setLastGeneratedAt($lastGeneratedAt);
        $this->getLastGeneratedAt()->shouldReturn($lastGeneratedAt);
    }
}
