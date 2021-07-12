<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Provider;

use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Generator\InvoiceFileNameGeneratorInterface;

final class InvoiceFilePathProvider implements InvoiceFilePathProviderInterface
{
    /** @var InvoiceFileNameGeneratorInterface */
    private $invoiceFileNameGenerator;

    /** @var string */
    private $invoicesDirectory;

    public function __construct(InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator, string $invoicesDirectory)
    {
        $this->invoiceFileNameGenerator = $invoiceFileNameGenerator;
        $this->invoicesDirectory = $invoicesDirectory;
    }

    public function provide(InvoiceInterface $invoice): string
    {
        return $this->invoicesDirectory.'/'.$this->invoiceFileNameGenerator->generateForPdf($invoice);
    }
}
