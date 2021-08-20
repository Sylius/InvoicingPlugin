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

namespace spec\Sylius\InvoicingPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;
use Sylius\InvoicingPlugin\Entity\TaxItem;
use Sylius\InvoicingPlugin\Factory\TaxItemFactoryInterface;

class TaxItemFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(TaxItem::class);
    }

    function it_implements_tax_item_factory_interface(): void
    {
        $this->shouldImplement(TaxItemFactoryInterface::class);
    }

    function it_does_not_allow_to_create_with_empty_data(): void
    {
        $this->shouldThrow(UnsupportedMethodException::class)->during('createNew');
    }

    function it_allows_only_for_injection_of_fqcn_that_are_tax_item_or_its_descendants(): void
    {
        $this->beConstructedWith(\stdClass::class);

        $this->shouldThrow(\DomainException::class)->duringInstantiation();
    }

    function it_creates_tax_item_from_label_and_amount(): void
    {
        $this->createWithData('Tax', 17)->shouldBeLike(new TaxItem('Tax', 17));
    }
}
