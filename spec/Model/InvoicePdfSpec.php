<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Model;

use PhpSpec\ObjectBehavior;

final class InvoicePdfSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith('2018_01_0000002.pdf', 'pdf content');
    }

    function it_has_filename(): void
    {
        $this->filename()->shouldReturn('2018_01_0000002.pdf');
    }

    function it_has_content(): void
    {
        $this->content()->shouldReturn('pdf content');
    }
}
