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

namespace Tests\Sylius\InvoicingPlugin\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Account\Order\IndexPageInterface;
use Webmozart\Assert\Assert;

final class AccountContext implements Context
{
    public function __construct(private IndexPageInterface $orderIndexPage)
    {
    }

    #[Then('I should be able to download an invoice from my orders list')]
    public function iShouldBeAbleToDownloadAnInvoiceFromMyOrdersList(): void
    {
        Assert::true($this->orderIndexPage->hasInvoiceLinks());
    }

    #[Then('I should not be able to download an invoice from my orders list')]
    public function iShouldNotBeAbleToDownloadAnInvoiceFromMyOrdersList(): void
    {
        Assert::true($this->orderIndexPage->hasPlainInvoiceNumbers());
    }
}
