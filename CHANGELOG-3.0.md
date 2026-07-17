# CHANGELOG

### v3.0.0 (2025-10-24)

- [#397](https://github.com/Sylius/InvoicingPlugin/pull/397) Isolate plugin messaging: Introduce dedicated event & command buses for InvoicingPlugin ([@tomkalon](https://github.com/tomkalon))
- [#398](https://github.com/Sylius/InvoicingPlugin/pull/398) Persisted PDF path & file flow
    - `Invoice`: added `path` field (UNIQUE).
    - `InvoiceFactory`: inject `InvoiceFileNameGeneratorInterface`; use `generateForPdf($number)` to set `path` on creation.
    - `InvoiceFileProvider`: removed dependency on file-name generator; added `%sylius_invoicing.pdf_generator.enabled%`; now relies on `Invoice::path()`.
    - `InvoiceCreator`: removed `InvoicePdfFileGeneratorInterface` and `InvoiceFileManagerInterface`; PDF is no longer generated on invoice creation—it's generated lazily on first download/provide.
    - `InvoiceFileNameGeneratorInterface::generateForPdf()` now accepts `string $invoiceNumber` instead of `InvoiceInterface`.
    - `InvoiceFileNameGeneratorInterface::generateForPdf()` can prefix filenames based on the configured sequence scope (`global` – default, `monthly`, `annually`).
    - Sequence scope is configured via `sylius_invoicing.sequence.scope` semantic configuration, validated at container build time and injected as `InvoiceSequenceScopeEnum`.
    - `InvoiceSequence`: `year`, `month` and `type` columns are `NOT NULL` (defaults: `0`, `0`, `global`) with a unique index on `(type, year, month)` guarding against duplicate sequences under concurrency.
    - `InvoicePdfFileGenerator`: removed `InvoiceFileNameGeneratorInterface` from constructor; filename is taken from `Invoice::path()`; update DI to drop the generator argument.
