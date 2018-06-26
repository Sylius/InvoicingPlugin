<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Order;

use Sylius\Behat\Page\SymfonyPageInterface;

interface ShowPageInterface extends SymfonyPageInterface
{
    public function hasRelatedInvoices(int $count): bool;

    public function clickOnFirstInvoiceId(): void;

    public function downloadFirstInvoice(): void;

    public function isPdfFileDownloaded(): bool;
}
