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

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Account\Order;

use Sylius\Behat\Page\Shop\Account\Order\IndexPage as BaseIndexPage;

final class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function hasInvoiceLinks(): bool
    {
        return $this->getDocument()->has('css', '[data-test-grid-table-body] [data-test-invoice-link]');
    }

    public function hasPlainInvoiceNumbers(): bool
    {
        return $this->getDocument()->has('css', '[data-test-grid-table-body] [data-test-invoice-number]');
    }
}
