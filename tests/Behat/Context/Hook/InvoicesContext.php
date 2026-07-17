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
        $this->clearDirectory($this->invoicesSavePath);
    }

    private function clearDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        foreach (scandir($directory) as $file) {
            if (in_array($file, ['.', '..'], true)) {
                continue;
            }

            $path = $directory . '/' . $file;

            if (is_dir($path)) {
                $this->clearDirectory($path);
                rmdir($path);

                continue;
            }

            unlink($path);
        }
    }
}
