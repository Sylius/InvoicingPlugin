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

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Order;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface ShowPageInterface extends SymfonyPageInterface
{
    public function countRelatedInvoices(): int;

    public function clickOnFirstInvoiceId(): void;

    public function downloadFirstInvoice(): void;

    public function resendFirstInvoice(): void;

    public function isPdfFileDownloaded(): bool;

    public function hasDownloadButtonForInvoice(): bool;
}
