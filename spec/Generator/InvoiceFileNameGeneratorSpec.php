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

namespace spec\Sylius\InvoicingPlugin\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceFileNameGeneratorInterface;

final class InvoiceFileNameGeneratorSpec extends ObjectBehavior
{
    function it_implements_invoice_file_name_generator_interface(): void
    {
        $this->shouldImplement(InvoiceFileNameGeneratorInterface::class);
    }

    function it_generates_invoice_file_name_based_on_its_number(InvoiceInterface $invoice): void
    {
        $invoice->number()->willReturn('2020/01/02/000333');

        $this->generateForPdf($invoice)->shouldReturn('2020_01_02_000333.pdf');
    }
}
