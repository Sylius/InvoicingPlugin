# UPGRADE FROM 2.2 TO 3.0

## Changes

1. Persisted PDF path on `Invoice`:

- Added to `Invoice` new `path` field (unique) storing the final PDF location (e.g. annually/2025_10_000000001.pdf).

2. Filename generation moved from `InvoiceCreator` to `InvoiceFactory`.

`InvoiceCreator` no longer generates PDFs at creation time.

PDFs are generated on first provide/download via the provider.

```xml
<service id="sylius_invoicing.custom_factory.invoice" class="Sylius\InvoicingPlugin\Factory\InvoiceFactory">
    <argument>%sylius_invoicing.model.invoice.class%</argument>
    <argument type="service" id="sylius_invoicing.factory.shop_billing_data" />
+   <argument type="service" id="sylius_invoicing.generator.invoice_file_name" />
</service>
```

```xml
<service id="sylius_invoicing.creator.invoice" class="Sylius\InvoicingPlugin\Creator\InvoiceCreator">
    <argument type="service" id="sylius_invoicing.repository.invoice" />
    <argument type="service" id="sylius.repository.order" />
    <argument type="service" id="sylius_invoicing.generator.invoice" />
-   <argument type="service" id="sylius_invoicing.generator.invoice_pdf_file" />
-   <argument type="service" id="sylius_invoicing.manager.invoice_file" />
-   <argument>%sylius_invoicing.pdf_generator.enabled%</argument>
</service>
```

3. `InvoiceFactory` now depends on `InvoiceFileNameGeneratorInterface`.
```xml
<service id="sylius_invoicing.custom_factory.invoice" class="Sylius\InvoicingPlugin\Factory\InvoiceFactory">
    <argument>%sylius_invoicing.model.invoice.class%</argument>
    <argument type="service" id="sylius_invoicing.factory.shop_billing_data" />
+   <argument type="service" id="sylius_invoicing.generator.invoice_file_name" />
</service>
```

On creation, it calls:
```php
$fileName = $invoiceFileNameGenerator->generateForPdf($number);
```
and passes it to the `Invoice` constructor as `$path`.

4. `InvoiceFileProvider` is now the primary orchestrator of PDF generation

Removed `InvoiceFileNameGeneratorInterface` from `InvoiceFileProvider`.

Added `sylius_invoicing.pdf_generator.enabled` parameter to constructor.

```xml
<service id="sylius_invoicing.provider.invoice_file" class="Sylius\InvoicingPlugin\Provider\InvoiceFileProvider">
-   <argument type="service" id="sylius_invoicing.generator.invoice_file_name" />
    <argument type="service" id="gaufrette.sylius_invoicing_invoice_filesystem" />
    <argument type="service" id="sylius_invoicing.generator.invoice_pdf_file" />
    <argument type="service" id="sylius_invoicing.manager.invoice_file" />
    <argument>%sylius_invoicing.invoice_save_path%</argument>
+   <argument>%sylius_invoicing.pdf_generator.enabled%</argument>
</service>
```

5. `InvoiceFileNameGenerator` signature & scoping

BC break: `generateForPdf()` now accepts string $invoiceNumber (not `InvoiceInterface`).

```php
// before:
public function generateForPdf(InvoiceInterface $invoice): string;

// after:
public function generateForPdf(string $invoiceNumber): string;
```

6. Can prefix filenames based on `SYLIUS_INVOICING_SEQUENCE_SCOPE`:

>global (default): no prefix
> 
>monthly: monthly/…
> 
>annually: annually/…

7. `InvoicePdfFileGenerator` simplified:

- Removed dependency on InvoiceFileNameGeneratorInterface.

- Uses `Invoice::path()` as the filename:

```xml
<service id="sylius_invoicing.generator.invoice_pdf_file" class="Sylius\InvoicingPlugin\Generator\InvoicePdfFileGenerator">
    <argument type="service" id="sylius_invoicing.generator.twig_to_pdf" />
    <argument type="service" id="file_locator" />
-   <argument type="service" id="sylius_invoicing.generator.invoice_file_name" />
    <argument>@SyliusInvoicingPlugin/shared/download/pdf.html.twig</argument>
    <argument>%sylius_invoicing.template.logo_file%</argument>
</service>
```

```php
$filename = $invoice->path();
```
