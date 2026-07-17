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

namespace Sylius\InvoicingPlugin\Generator;

use Sylius\InvoicingPlugin\Enum\InvoiceSequenceScopeEnum;

final class InvoiceFileNameGenerator implements InvoiceFileNameGeneratorInterface
{
    private const PDF_FILE_EXTENSION = '.pdf';

    public function __construct(
        private readonly InvoiceSequenceScopeEnum $scope = InvoiceSequenceScopeEnum::GLOBAL,
    ) {
    }

    public function generateForPdf(string $invoiceNumber): string
    {
        $prefix = InvoiceSequenceScopeEnum::GLOBAL === $this->scope ? '' : $this->scope->value . '/';

        return $prefix . str_replace('/', '_', $invoiceNumber) . self::PDF_FILE_EXTENSION;
    }
}
