<?php

declare(strict_types=1);

namespace spec\Sylius\InvoicingPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;

final class BillingDataSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(
            'John',
            'Doe',
            'US',
            'Foo Street 3/44',
            'New York',
            '24154',
            'IE',
            'Utah',
            'Company Ltd.'
        );
    }

    function it_implements_billing_data_interface(): void
    {
        $this->shouldImplement(BillingDataInterface::class);
    }

    function it_implements_resource_interface(): void
    {
        $this->shouldImplement(ResourceInterface::class);
    }

    function it_has_proper_billing_data(): void
    {
        $this->firstName()->shouldReturn('John');
        $this->lastName()->shouldReturn('Doe');
        $this->countryCode()->shouldReturn('US');
        $this->street()->shouldReturn('Foo Street 3/44');
        $this->city()->shouldReturn('New York');
        $this->postcode()->shouldReturn('24154');
        $this->provinceCode()->shouldReturn('IE');
        $this->provinceName()->shouldReturn('Utah');
        $this->company()->shouldReturn('Company Ltd.');
    }
}
