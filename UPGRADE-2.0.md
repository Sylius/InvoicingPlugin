# UPGRADE FROM 1.X TO 2.0

1. Support for Sylius 2.0 has been added, it is now the recommended Sylius version to use with InvoicingPlugin.

1. Support for Sylius 1.X has been dropped, upgrade your application to [Sylius 2.0](https://github.com/Sylius/Sylius/blob/2.0/UPGRADE-2.0.md).

1. The minimum supported version of PHP has been increased to 8.2.

1. The `sylius_invoicing_plugin_admin_order_invoices_partial` and `sylius_invoicing_plugin_shop_order_invoices_partial` partials 
   have been replaced by `Sylius\InvoicingPlugin\Twig\Component\Invoice\ListComponent` twig component.

1. Doctrine migrations have been regenerated, meaning all previous migration files have been removed and their content 
   is now in a single migration file. To apply the new migration and get rid of the old entries run migrations as usual:

   ```bash
       bin/console doctrine:migrations:migrate --no-interaction
   ```
