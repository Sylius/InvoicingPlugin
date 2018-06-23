<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin;

use Sylius\Behat\Page\SymfonyPage;

final class OrderShowPage extends SymfonyPage implements OrderShowPageInterface
{
    public function hasRelatedInvoices(int $count): bool
    {
        return count($this->getInvoiceList()) === $count + 1;
    }

    public function clickOnFirstInvoiceId(): void
    {
        $invoice = $this->getInvoiceList()[1];

        $this->getSession()->getPage()->clickLink($invoice->findAll('css', 'td')[0]->getText());
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_admin_order_show';
    }

    private function getInvoiceList(): array
    {
        return $this->getSession()
            ->getPage()
            ->find('css', '#orderInvoices')
            ->findAll('css', 'tr');
    }
}
