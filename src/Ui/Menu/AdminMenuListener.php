<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Ui\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function __invoke(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        /** @var ItemInterface $salesMenu */
        $salesMenu = $menu->getChild('sales');

        $salesMenu
            ->addChild('invoices', ['route' => 'sylius_invoicing_plugin_admin_invoice_index'])
                ->setLabel('sylius_invoicing_plugin.ui.invoices')
                ->setLabelAttribute('icon', 'file')
        ;
    }
}
