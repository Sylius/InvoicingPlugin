<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Saver;

use Sylius\InvoicingPlugin\Model\InvoicePdf;

interface InvoiceFileSaverInterface
{
    public function save(InvoicePdf $file): void;
}
