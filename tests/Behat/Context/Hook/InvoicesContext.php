<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Hook;

use Behat\Behat\Context\Context;

final class InvoicesContext implements Context
{
    private string $invoicesSavePath;

    public function __construct(string $invoicesSavePath)
    {
        $this->invoicesSavePath = $invoicesSavePath;
    }

    /**
     * @BeforeScenario
     */
    public function clearInvoicesPath(): void
    {
        foreach (scandir($this->invoicesSavePath) as $file) {
            if (is_file($this->invoicesSavePath.'/'.$file)) {
                unlink($this->invoicesSavePath.'/'.$file);
            }
        }
    }
}
