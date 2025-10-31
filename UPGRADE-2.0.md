# UPGRADE FROM 2.0.2 TO 2.0.3

### Deprecations
- `Sylius\InvoicingPlugin\Provider\UnitNetPriceProvider` — **deprecated since 2.0** and will be removed in 3.0. 
- The `orderItemUnitsToLineItemsConverter` argument and property in `Sylius\InvoicingPlugin\Generator\InvoiceGenerator` — **deprecated since 2.0.3**, to be removed in 3.0.
- Using `InvoiceGenerator` without providing `orderItemsToLineItemsConverter` is **deprecated since 2.0** and will become an error in 3.0.

```diff
<service id="sylius_invoicing.generator.invoice" class="Sylius\InvoicingPlugin\Generator\InvoiceGenerator">
    <argument type="service" id="sylius_invoicing.generator.invoice_identifier" />
    <argument type="service" id="sylius_invoicing.generator.invoice_number" />
    <argument type="service" id="sylius_invoicing.custom_factory.invoice" />
    <argument type="service" id="sylius_invoicing.factory.billing_data" />
    <argument type="service" id="sylius_invoicing.factory.shop_billing_data" />
    <argument type="service" id="sylius_invoicing.converter.order_item_units_to_line_items" />
    <argument type="service" id="sylius_invoicing.converter.shipping_adjustments_to_line_items" />
    <argument type="service" id="sylius_invoicing.converter.tax_items" />
+   <argument type="service" id="sylius_invoicing.converter.order_item_to_line_items" />
</service>
```

### Changed
- `InvoiceGenerator` now prefers `orderItemsToLineItemsConverter`; if it is not provided, it falls back to the (deprecated) `orderItemUnitsToLineItemsConverter` and emits deprecation warnings.

### Removed (since 3.0)
- `UnitNetPriceProvider`
- `orderItemUnitsToLineItemsConverter` from `InvoiceGenerator` (argument and property)

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
