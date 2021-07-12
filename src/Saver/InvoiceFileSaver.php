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

namespace Sylius\InvoicingPlugin\Saver;

use Gaufrette\FilesystemInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;

final class InvoiceFileSaver implements InvoiceFileSaverInterface
{
    /** @var FilesystemInterface */
    private $filesystem;

    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function save(InvoicePdf $file): void
    {
        $this->filesystem->write($file->filename(), $file->content());
    }
}
