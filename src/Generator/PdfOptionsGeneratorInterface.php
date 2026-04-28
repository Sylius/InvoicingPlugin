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

/**
 * @deprecated since sylius/invoicing-plugin 2.2, use sylius/pdf-generation-bundle's adapter options instead.
 */
interface PdfOptionsGeneratorInterface
{
    public function generate(): array;
}
