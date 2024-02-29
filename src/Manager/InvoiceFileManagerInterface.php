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

use Sylius\InvoicingPlugin\Model\InvoicePdf;

interface InvoiceFileManagerInterface
{
    public function save(InvoicePdf $file): void;

    public function remove(InvoicePdf $file): void;
}
