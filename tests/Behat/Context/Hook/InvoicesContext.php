<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Hook;

use Behat\Behat\Context\Context;

final class InvoicesContext implements Context
{
    public function __construct(private string $invoicesSavePath)
    {
    }

    /**
     * @BeforeScenario
     */
    public function clearInvoicesPath(): void
    {
        if (!is_dir($this->invoicesSavePath)) {
            return;
        }

        foreach (scandir($this->invoicesSavePath) as $file) {
            if (is_file($this->invoicesSavePath.'/'.$file)) {
                unlink($this->invoicesSavePath.'/'.$file);
            }
        }
    }
}
