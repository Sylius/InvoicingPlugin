<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin\Order;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

final class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_admin_order_show';
    }

    public function countRelatedInvoices(): int
    {
        return count($this->getInvoicesList()) -1;
    }

    public function clickOnFirstInvoiceId(): void
    {
        $invoice = $this->getFirstInvoice();
        $invoiceId = $invoice->findAll('css', 'td')[0]->getText();

        $invoice->clickLink($invoiceId);
    }

    public function downloadFirstInvoice(): void
    {
        $invoice = $this->getFirstInvoice();
        $invoice->clickLink('Download');
    }

    public function resendFirstInvoice(): void
    {
        $invoice = $this->getFirstInvoice();
        $invoice->clickLink('Resend');
    }

    public function isPdfFileDownloaded(): bool
    {
        $session = $this->getSession();
        $headers = $session->getResponseHeaders();

        return
            200 === $session->getStatusCode() &&
            'application/pdf' === $headers['content-type'][0]
        ;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'invoices' => '#order-invoices',
        ]);
    }

    private function getFirstInvoice(): NodeElement
    {
        return $this->getInvoicesList()[1];
    }

    private function getInvoicesList(): array
    {
        return $this->getElement('invoices')->findAll('css', 'tr');
    }
}
