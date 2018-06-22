<?php

declare(strict_types=1);

namespace Tests\Sylius\InvoicingPlugin\Behat\Page\Admin;

use Sylius\Behat\Page\SymfonyPage;

final class OrderShowPage extends SymfonyPage implements OrderShowPageInterface
{
    public function hasRelatedInvoices(int $count): bool
    {
        $invoices = $this->getSession()->getPage()->find('css', '#invoices');


    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_admin_order_show';
    }
}
