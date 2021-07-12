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

namespace Sylius\InvoicingPlugin\Provider;

use Gaufrette\FilesystemInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceFileNameGeneratorInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;

final class InvoiceFileProvider implements InvoiceFileProviderInterface
{
    /** @var InvoiceFileNameGeneratorInterface */
    private $invoiceFileNameGenerator;

    /** @var FilesystemInterface */
    private $filesystem;

    /** @var string */
    private $invoicesDirectory;

    public function __construct(
        InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator,
        FilesystemInterface $filesystem,
        string $invoicesDirectory
    ) {
        $this->invoiceFileNameGenerator = $invoiceFileNameGenerator;
        $this->filesystem = $filesystem;
        $this->invoicesDirectory = $invoicesDirectory;
    }

    public function provide(InvoiceInterface $invoice): InvoicePdf
    {
        $invoiceFileName = $this->invoiceFileNameGenerator->generateForPdf($invoice);
        $invoiceFile = $this->filesystem->get($invoiceFileName);

        $invoicePdf = new InvoicePdf($invoiceFileName, $invoiceFile->getContent());
        $invoicePdf->setFullPath($this->invoicesDirectory . '/' . $invoiceFileName);

        return $invoicePdf;
    }
}
