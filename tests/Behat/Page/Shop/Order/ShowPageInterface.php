<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Shop\Order;

interface ShowPageInterface
{
    public function downloadFirstInvoice(): void;
    
    public function isPdfFileDownloaded(): bool;
}
