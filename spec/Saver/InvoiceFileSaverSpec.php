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

namespace spec\Sylius\InvoicingPlugin\Saver;

use Gaufrette\FilesystemInterface;
use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Sylius\InvoicingPlugin\Saver\InvoiceFileSaverInterface;

final class InvoiceFileSaverSpec extends ObjectBehavior
{
    function let(FilesystemInterface $filesystem): void
    {
        $this->beConstructedWith($filesystem);
    }

    function it_implements_invoice_file_saver_interface(): void
    {
        $this->shouldImplement(InvoiceFileSaverInterface::class);
    }

    function it_saves_invoice_pdf_in_given_filesystem(FilesystemInterface $filesystem): void
    {
        $filesystem->write('2020_01_01_invoice.pdf', 'CONTENT')->shouldBeCalled();

        $this->save(new InvoicePdf('2020_01_01_invoice.pdf', 'CONTENT'));
    }
}
