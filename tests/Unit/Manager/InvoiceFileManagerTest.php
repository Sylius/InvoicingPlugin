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

namespace Tests\Sylius\InvoicingPlugin\Unit\Manager;

use Gaufrette\FilesystemInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\InvoicingPlugin\Manager\InvoiceFileManager;
use Sylius\InvoicingPlugin\Manager\InvoiceFileManagerInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;

final class InvoiceFileManagerTest extends TestCase
{
    #[Test]
    public function it_implements_invoice_file_saver_interface(): void
    {
        $filesystem = $this->createMock(FilesystemInterface::class);
        $manager = new InvoiceFileManager($filesystem);

        self::assertInstanceOf(InvoiceFileManagerInterface::class, $manager);
    }

    #[Test]
    public function it_saves_invoice_pdf_in_given_filesystem(): void
    {
        $filesystem = $this->createMock(FilesystemInterface::class);

        $filesystem
            ->expects(self::once())
            ->method('write')
            ->with('2020_01_01_invoice.pdf', 'CONTENT');

        $manager = new InvoiceFileManager($filesystem);
        $manager->save(new InvoicePdf('2020_01_01_invoice.pdf', 'CONTENT'));
    }

    #[Test]
    public function it_removes_invoice_pdf_in_given_filesystem(): void
    {
        $filesystem = $this->createMock(FilesystemInterface::class);

        $filesystem
            ->expects(self::once())
            ->method('delete')
            ->with('2020_01_01_invoice.pdf');

        $manager = new InvoiceFileManager($filesystem);
        $manager->remove(new InvoicePdf('2020_01_01_invoice.pdf', 'CONTENT'));
    }
}
