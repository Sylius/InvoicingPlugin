# UPGRADE FROM 2.2 TO 2.3

1. Sylius 2.3 no longer ships `knplabs/gaufrette` and `knplabs/knp-gaufrette-bundle`, so the plugin now requires them itself.
   `knplabs/knp-gaufrette-bundle` 1.0 supports Symfony 8, but it also upgrades `knplabs/gaufrette` to 1.0,
   so verify that the Gaufrette adapters you use still work with it.

   Make sure `Knp\Bundle\GaufretteBundle\KnpGaufretteBundle` is registered in your `config/bundles.php`.
   Nothing else changes: invoices are still stored through the `gaufrette.sylius_invoicing_invoice_filesystem` filesystem.

   Gaufrette is used by the legacy PDF generator and will be removed in 3.0, together with the `sylius_invoicing.pdf_generator.legacy` option.
   Migrating to the `SyliusPdfGenerationBundle` integration is recommended:

    ```yaml
    sylius_invoicing:
        pdf_generator:
            legacy: false
    ```

   With the integration enabled, the storage of invoices is configured through the `sylius_invoicing` context
   of `SyliusPdfGenerationBundle`, which supports the `filesystem`, `flysystem` and `gaufrette` storage types.
   It defaults to the Gaufrette filesystem above. The plugin also provides the `sylius_invoicing.storage.invoice` Flysystem storage,
   using the local adapter in the same `%sylius_invoicing.invoice_save_path%` directory, which will become the default in 3.0.
   To switch to it already, so existing invoices are still found:

    ```yaml
    sylius_pdf_generation:
        contexts:
            sylius_invoicing:
                storage:
                    type: flysystem
                    filesystem: sylius_invoicing.storage.invoice
    ```

   To store invoices elsewhere (e.g. on S3), redefine the `sylius_invoicing.storage.invoice` storage under `flysystem.storages`,
   see the [FlysystemBundle documentation](https://github.com/thephpleague/flysystem-bundle/blob/3.x/docs/2-cloud-storage-providers.md).
   See the [SyliusPdfGenerationBundle documentation](https://github.com/Sylius/PdfGenerationBundle#configuration) for the other storage types.
