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

namespace spec\Sylius\InvoicingPlugin\Manager;

use Gaufrette\FilesystemInterface;
use PhpSpec\ObjectBehavior;
use Sylius\InvoicingPlugin\Manager\InvoiceFileManagerInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;

final class InvoiceFileManagerSpec extends ObjectBehavior
{
    function let(FilesystemInterface $filesystem): void
    {
        $this->beConstructedWith($filesystem);
    }

    function it_implements_invoice_file_saver_interface(): void
    {
        $this->shouldImplement(InvoiceFileManagerInterface::class);
    }

    function it_saves_invoice_pdf_in_given_filesystem(FilesystemInterface $filesystem): void
    {
        $filesystem->write('2020_01_01_invoice.pdf', 'CONTENT')->shouldBeCalled();

        $this->save(new InvoicePdf('2020_01_01_invoice.pdf', 'CONTENT'));
    }

    function it_removes_invoice_pdf_in_given_filesystem(FilesystemInterface $filesystem): void
    {
        $filesystem->delete('2020_01_01_invoice.pdf')->shouldBeCalled();

        $this->remove(new InvoicePdf('2020_01_01_invoice.pdf', 'CONTENT'));
    }
}
