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

namespace Sylius\InvoicingPlugin\Generator;

use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

final class InvoiceFileNameGenerator implements InvoiceFileNameGeneratorInterface
{
    private const PDF_FILE_EXTENSION = '.pdf';

    public function generateForPdf(InvoiceInterface $invoice): string
    {
        return str_replace('/', '_', $invoice->number()) . self::PDF_FILE_EXTENSION;
    }
}
