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

namespace spec\Sylius\InvoicingPlugin\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;
use Sylius\InvoicingPlugin\Entity\LineItem;
use Sylius\InvoicingPlugin\Factory\LineItemFactoryInterface;

class LineItemFactorySpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(LineItem::class);
    }

    function it_implements_line_item_factory_interface(): void
    {
        $this->shouldImplement(LineItemFactoryInterface::class);
    }

    function it_does_not_allow_to_create_with_empty_data(): void
    {
        $this->shouldThrow(UnsupportedMethodException::class)->during('createNew');
    }

    function it_allows_only_for_injection_of_fqcn_that_are_line_item_or_its_descendants(): void
    {
        $this->beConstructedWith(\stdClass::class);

        $this->shouldThrow(\DomainException::class)->duringInstantiation();
    }

    function it_creates_line_items_with_data(): void
    {
        $this
            ->createWithData('Mjolnir', 2, 6000, 5000, 10000, 1000, 11000, null, 'MJOLNIR', '10%')
            ->shouldBeLike(new LineItem(
                'Mjolnir',
                2,
                6000,
                5000,
                10000,
                1000,
                11000,
                null,
                'MJOLNIR',
                '10%',
            ))
        ;

        $this
            ->createWithData('UPS', 1, 1000, 1000, 1000, 200, 1200, null, null, '20%')
            ->shouldBeLike(new LineItem(
                'UPS',
                1,
                1000,
                1000,
                1000,
                200,
                1200,
                null,
                null,
                '20%',
            ))
        ;
    }
}
