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

namespace Sylius\InvoicingPlugin\Manager;

use Gaufrette\FilesystemInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;

final class InvoiceFileManager implements InvoiceFileManagerInterface
{
    public function __construct(private readonly FilesystemInterface $filesystem)
    {
    }

    public function save(InvoicePdf $file): void
    {
        $this->filesystem->write($file->filename(), $file->content());
    }

    public function remove(InvoicePdf $file): void
    {
        $this->filesystem->delete($file->filename());
    }
}
