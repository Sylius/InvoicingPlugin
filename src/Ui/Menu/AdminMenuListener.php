<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Ui\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function __invoke(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $menu
            ->getChild('sales')
                ->addChild('invoices', ['route' => 'sylius_invoicing_plugin_admin_invoice_index'])
                    ->setLabel('sylius_invoicing_plugin.invoices')
                    ->setLabelAttribute('icon', 'star') // TODO: Find out what icon to use lol
        ;
    }
}
